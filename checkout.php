<?php
// Checkout page for Student Book Exchange.
// Manages a simple session-based cart for merchandise items
// and creates orders with related order_items records.
// Author: Danil Hordiienko, ATU student.

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/header.php';

// Ensure cart exists in the session as an associative array: merch_id => quantity.
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$infoMessage = null;
$errorMessage = null;

// Handle incoming POST actions: add, remove, confirm.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $merchId = (int)($_POST['merch_id'] ?? 0);
        if ($merchId > 0) {
            // Increment quantity for this merchandise item in the cart.
            if (!isset($_SESSION['cart'][$merchId])) {
                $_SESSION['cart'][$merchId] = 0;
            }
            $_SESSION['cart'][$merchId]++;
            $infoMessage = 'Item has been added to your cart.';
        }
    } elseif ($action === 'remove') {
        $merchId = (int)($_POST['merch_id'] ?? 0);
        if ($merchId > 0 && isset($_SESSION['cart'][$merchId])) {
            unset($_SESSION['cart'][$merchId]);
            $infoMessage = 'Item has been removed from your cart.';
        }
    } elseif ($action === 'confirm') {
        // Confirm the order and write to orders and order_items tables.
        if (empty($_SESSION['user_id'])) {
            $errorMessage = 'You need to be logged in to confirm an order.';
        } elseif (empty($_SESSION['cart'])) {
            $errorMessage = 'Your cart is empty.';
        } else {
            try {
                $pdo->beginTransaction();

                // Load all merch items that are in the cart.
                $ids = array_keys($_SESSION['cart']);
                $placeholders = implode(',', array_fill(0, count($ids), '?'));

                $stmt = $pdo->prepare("
                    SELECT id, price_cents
                    FROM merch
                    WHERE id IN ($placeholders)
                ");
                $stmt->execute($ids);
                $merchRows = $stmt->fetchAll();

                // Build a map: merch_id => price_cents.
                $priceMap = [];
                foreach ($merchRows as $row) {
                    $priceMap[$row['id']] = (int)$row['price_cents'];
                }

                // Calculate total and insert an order row.
                $totalCents = 0;
                foreach ($_SESSION['cart'] as $merchId => $qty) {
                    if (!isset($priceMap[$merchId])) {
                        continue;
                    }
                    $unitPrice = $priceMap[$merchId];
                    $totalCents += $unitPrice * $qty;
                }

                $orderStmt = $pdo->prepare("
                    INSERT INTO orders (user_id, status, total_cents)
                    VALUES (?, 'paid', ?)
                ");
                $orderStmt->execute([$_SESSION['user_id'], $totalCents]);
                $orderId = (int)$pdo->lastInsertId();

                // Insert line items for the order.
                $itemStmt = $pdo->prepare("
                    INSERT INTO order_items (order_id, merch_id, quantity, unit_price_cents)
                    VALUES (?, ?, ?, ?)
                ");

                foreach ($_SESSION['cart'] as $merchId => $qty) {
                    if (!isset($priceMap[$merchId])) {
                        continue;
                    }
                    $itemStmt->execute([
                        $orderId,
                        $merchId,
                        (int)$qty,
                        $priceMap[$merchId]
                    ]);
                }

                $pdo->commit();

                // Clear the cart after a successful order.
                $_SESSION['cart'] = [];
                $infoMessage = 'Your order has been placed successfully.';
            } catch (PDOException $e) {
                $pdo->rollBack();
                $errorMessage = 'Failed to create an order. Please try again later.';
            }
        }
    }
}

// Build cart details for display.
$cartItems = [];
$cartTotalCents = 0;

if (!empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $stmt = $pdo->prepare("
        SELECT id, name, price_cents
        FROM merch
        WHERE id IN ($placeholders)
    ");
    $stmt->execute($ids);
    $rows = $stmt->fetchAll();

    $map = [];
    foreach ($rows as $row) {
        $map[$row['id']] = $row;
    }

    foreach ($_SESSION['cart'] as $merchId => $qty) {
        if (!isset($map[$merchId])) {
            continue;
        }
        $row = $map[$merchId];
        $rowQty = (int)$qty;
        $subtotal = $rowQty * (int)$row['price_cents'];

        $cartItems[] = [
            'id'       => $row['id'],
            'name'     => $row['name'],
            'price'    => (int)$row['price_cents'],
            'quantity' => $rowQty,
            'subtotal' => $subtotal,
        ];

        $cartTotalCents += $subtotal;
    }
}
?>

<h1 class="h3 mb-3">Checkout</h1>

<?php if ($infoMessage): ?>
    <div class="alert alert-success"><?= htmlspecialchars($infoMessage) ?></div>
<?php endif; ?>

<?php if ($errorMessage): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($errorMessage) ?></div>
<?php endif; ?>

<?php if (empty($cartItems)): ?>
    <div class="alert alert-info">
        Your cart is currently empty. Go to the merch page to add some items.
    </div>
<?php else: ?>

    <table class="table table-bordered align-middle">
        <thead>
        <tr>
            <th>Item</th>
            <th style="width: 100px;">Quantity</th>
            <th style="width: 120px;">Price</th>
            <th style="width: 120px;">Subtotal</th>
            <th style="width: 80px;"></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($cartItems as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= $item['quantity'] ?></td>
                <td>€<?= number_format($item['price'] / 100, 2) ?></td>
                <td>€<?= number_format($item['subtotal'] / 100, 2) ?></td>
                <td>
                    <form method="post" action="checkout.php" class="m-0">
                        <input type="hidden" name="action" value="remove">
                        <input type="hidden" name="merch_id" value="<?= $item['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            Remove
                        </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
        <tr>
            <th colspan="3" class="text-end">Total</th>
            <th colspan="2">
                €<?= number_format($cartTotalCents / 100, 2) ?>
            </th>
        </tr>
        </tfoot>
    </table>

    <?php if (empty($_SESSION['user_id'])): ?>
        <div class="alert alert-warning">
            Please log in before confirming your order.
        </div>
    <?php else: ?>
        <form method="post" action="checkout.php" class="mt-3">
            <input type="hidden" name="action" value="confirm">
            <button type="submit" class="btn btn-primary">
                Confirm order
            </button>
        </form>
    <?php endif; ?>

<?php endif; ?>

<?php
require_once __DIR__ . '/footer.php';
?>