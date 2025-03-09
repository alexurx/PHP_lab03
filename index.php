<?php
declare(strict_types=1);

// Массив транзакций
$transactions = [
    [
        "id" => 1,
        "date" => "2019-01-01",
        "amount" => 100.00,
        "description" => "Payment for groceries",
        "merchant" => "SuperMart",
    ],
    [
        "id" => 2,
        "date" => "2020-02-15",
        "amount" => 75.50,
        "description" => "Dinner with friends",
        "merchant" => "Local Restaurant",
    ],
];

/**  
 * Функция поиска транзакции по ID
 * 
 * @param int $id - ID транзакции
 * @return array|null - Найденная транзакция или null
 */
function findTransactionById(int $id): ?array {
    global $transactions;
    foreach ($transactions as $t) {
        if ($t["id"] === $id) return $t;
    }
    return null;
}

/**
* Функция поиска транзакции по части описания
*
* @param string $descriptionPart - Часть описания
* @return array - Массив найденных транзакций
*/
function findTransactionByDescription(string $descriptionPart): array {
    global $transactions;
    return array_filter($transactions, fn($t) => stripos($t["description"], $descriptionPart) !== false);
}

/** Функция сортировки по дате (новые выше)
 * 
 * @return void
 */
function sortTransactionsByDate(): void {
    global $transactions;
    usort($transactions, fn($a, $b) => strtotime($b["date"]) - strtotime($a["date"]));
}

/** Функция сортировки по сумме (по убыванию)
 * 
 * @return void
 */
function sortTransactionsByAmount(): void {
    global $transactions;
    usort($transactions, fn($a, $b) => $b["amount"] <=> $a["amount"]);
}

/** Функция вычисления суммы всех транзакций
 * 
 * @param array $transactions - Массив транзакций
 * @return float - Сумма всех транзакций
 */
function calculateTotalAmount(array $transactions): float {
    return array_sum(array_column($transactions, "amount"));
}

/** Функция подсчета дней с момента транзакции
 * 
 * @param string $date - Дата транзакции
 * @return int - Количество дней с момента транзакции
 */
function daysSinceTransaction(string $date): int {
    $transactionDate = new DateTime($date);
    $now = new DateTime();
    return $now->diff($transactionDate)->days;
}

/** Обработка формы поиска
 * 
 * @var string $searchTerm - Поисковый запрос
 * @var array $searchResults - Результаты поиска
 */
$searchResults = [];
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["search"])) {
    $searchTerm = trim($_POST["search"]);
    $searchResults = findTransactionByDescription($searchTerm);
}

/** Обработка поиска по ID
 * 
 * @var int $searchId - ID для поиска
 * @var array|null $transactionById - Найденная транзакция
 */
$transactionById = null;
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["search_id"])) {
    $searchId = (int)$_POST["search_id"];
    $transactionById = findTransactionById($searchId);
}

/** Сортировка по выбранному параметру
 * 
 * @var string $_GET["sort"] - Параметр сортировки
 */
if (isset($_GET["sort"])) {
    if ($_GET["sort"] === "date") {
        sortTransactionsByDate();
    } elseif ($_GET["sort"] === "amount") {
        sortTransactionsByAmount();
    }
}

/** Функция добавления новой транзакции
 * 
 * @param int $id - ID транзакции
 * @param string $date - Дата транзакции
 * @param float $amount - Сумма транзакции
 * @param string $description - Описание транзакции
 * @param string $merchant - Название магазина
 * @return void
 */
function addTransaction(int $id, string $date, float $amount, string $description, string $merchant): void {
    global $transactions;
    $transactions[] = [
        "id" => $id,
        "date" => $date,
        "amount" => $amount,
        "description" => $description,
        "merchant" => $merchant
    ];
}

/** Добавление новой транзакции
 * 
 * @var int $id - ID транзакции
 * @var string $date - Дата транзакции
 * @var float $amount - Сумма транзакции
 * @var string $description - Описание транзакции
 * @var string $merchant - Название магазина
 */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add"])) {
    $id = count($transactions) + 1;
    addTransaction($id, $_POST["date"], (float)$_POST["amount"], $_POST["description"], $_POST["merchant"]);
}

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Transactions</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            padding: 20px;
            background-color: #f8f8f8;
        }
        .header {
            font-size: 18px;
            font-weight: bold;
            padding: 10px;
            background-color: white;
            border: 1px solid #000;
            display: inline-block;
            margin-bottom: 20px;
        }
        table {
            width: 80%;
            margin: auto;
            border-collapse: collapse;
            background: white;
        }
        th, td {
            border: 1px solid black;
            padding: 10px;
        }
        th {
            background: #ddd;
        }
        form {
            margin: 20px;
        }
        input, button {
            padding: 10px;
            margin: 5px;
        }
    </style>
</head>
<body>

<div class="header">
    Transactions | <a href="?sort=date">Sort by Date</a> | <a href="?sort=amount">Sort by Amount</a>
</div>

<h2>Bank Transactions</h2>

<form method="POST">
    <input type="text" name="search" placeholder="Search description...">
    <button type="submit">Find</button>
</form>

<form method="POST">
    <input type="number" name="search_id" placeholder="Search by ID">
    <button type="submit">Find by ID</button>
</form>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Date</th>
            <th>Amount</th>
            <th>Description</th>
            <th>Merchant</th>
            <th>Days Ago</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($transactions as $t): ?>
        <tr>
            <td><?= $t["id"] ?></td>
            <td><?= $t["date"] ?></td>
            <td><?= number_format($t["amount"], 2) ?> $</td>
            <td><?= $t["description"] ?></td>
            <td><?= $t["merchant"] ?></td>
            <td><?= daysSinceTransaction($t["date"]) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5"><strong>Total:</strong></td>
            <td><strong><?= number_format(calculateTotalAmount($transactions), 2) ?> $</strong></td>
        </tr>
    </tfoot>
</table>

<h2>Search Results</h2>
<?php if ($searchResults): ?>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Date</th>
            <th>Amount</th>
            <th>Description</th>
            <th>Merchant</th>
            <th>Days Ago</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($searchResults as $t): ?>
        <tr>
            <td><?= $t["id"] ?></td>
            <td><?= $t["date"] ?></td>
            <td><?= number_format($t["amount"], 2) ?> $</td>
            <td><?= $t["description"] ?></td>
            <td><?= $t["merchant"] ?></td>
            <td><?= daysSinceTransaction($t["date"]) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<p>No results found.</p>
<?php endif; ?>

<h2>Search by ID Result</h2>
<?php if ($transactionById): ?>
<table>
    <tr>
        <th>ID</th>
        <th>Date</th>
        <th>Amount</th>
        <th>Description</th>
        <th>Merchant</th>
        <th>Days Ago</th>
    </tr>
    <tr>
        <td><?= $transactionById["id"] ?></td>
        <td><?= $transactionById["date"] ?></td>
        <td><?= number_format($transactionById["amount"], 2) ?> $</td>
        <td><?= $transactionById["description"] ?></td>
        <td><?= $transactionById["merchant"] ?></td>
        <td><?= daysSinceTransaction($transactionById["date"]) ?></td>
    </tr>
</table>
<?php else: ?>
<p>No transaction found with this ID.</p>
<?php endif; ?>

<h2>Add New Transaction</h2>
<form method="POST">
    <input type="date" name="date" required>
    <input type="number" step="0.01" name="amount" placeholder="Amount" required>
    <input type="text" name="description" placeholder="Description" required>
    <input type="text" name="merchant" placeholder="Merchant" required>
    <button type="submit" name="add">Add Transaction</button>
</form>

</body>
</html>
