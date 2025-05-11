<?php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["view"])) {
    $startDate = $_POST["start"];
    $endDate = $_POST["end"];

    // Count total amount of paymants for donation posts
    $query = "SELECT apartment_no, SUM(amount) AS total_amount
              FROM payments
              WHERE date_created BETWEEN ? AND ?
              GROUP BY apartment_no";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();

    // Process the query result and prepare data for the graph
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $apartment_no  = $row['apartment_no'];

        // Query to retrieve donation post name
        $sql2 ="SELECT apartment_no FROM apartments WHERE apartment_no = ?";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("i", $apartment_no);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        $row2 = $result2->fetch_assoc();

        // Add data to the array
        $data[] = [
            "apartment_no" => $row2["apartment_no"],
            "total_rent" => $row["total_amount"]
        ];
        $stmt2->close();
    }
    $stmt->close();
    $conn->close();

    // Convert the data to JSON format for Chart.js
    $jsonData = json_encode($data);
}
?>