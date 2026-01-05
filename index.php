<?php
// Σύνδεση με τη βάση δεδομένων
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "LAXANAGORA";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Σφάλμα σύνδεσης: " . $conn->connect_error);
}

// Επεξεργασία φορμών
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Εισαγωγή Παραγωγού
    if (isset($_POST['add_producer'])) {
        $id = $_POST['producer_id'];
        $name = $_POST['producer_name'];
        $region = $_POST['producer_region'];
        $license_id = $_POST['license_id'];
        
        // Έλεγχος αν υπάρχει ήδη η άδεια
        $check_license = $conn->query("SELECT * FROM ADEIA WHERE ID_ADEIAS = '$license_id'");
        
        if ($check_license->num_rows == 0) {
            // Αν δεν υπάρχει, δημιουργία νέας άδειας με ημερομηνίες NULL
            $current_date = date('Y-m-d');
            $future_date = date('Y-m-d', strtotime('+1 year'));
            
            $license_sql = "INSERT INTO ADEIA (ID_ADEIAS, HM_ENARXIS, HM_LIXIS) 
                           VALUES ('$license_id', '$current_date', '$future_date')";
            
            if (!$conn->query($license_sql)) {
                echo "Σφάλμα δημιουργίας άδειας: " . $conn->error;
            }
        }
        
        // Εισαγωγή παραγωγού
        $sql = "INSERT INTO PARAGOGOS (ID_PARAGOGOU, ONOMATEPONIMO, PERIOXI, ID_ADEIAS) 
                VALUES ('$id', '$name', '$region', '$license_id')";
        if (!$conn->query($sql)) {
            echo "Σφάλμα: " . $conn->error;
        }
    }
    
    // Διαγραφή Παραγωγού
    if (isset($_POST['delete_producer'])) {
        $id = $_POST['delete_producer_id'];
        
        // Βρίσκουμε το ID της άδειας που έχει ο παραγωγός
        $license_query = $conn->query("SELECT ID_ADEIAS FROM PARAGOGOS WHERE ID_PARAGOGOU='$id'");
        if ($license_query->num_rows > 0) {
            $license_data = $license_query->fetch_assoc();
            $license_id = $license_data['ID_ADEIAS'];
            
            // Διαγραφή παραγωγού
            $sql = "DELETE FROM PARAGOGOS WHERE ID_PARAGOGOU='$id'";
            if ($conn->query($sql)) {
                // Διαγραφή άδειας μετά την επιτυχημένη διαγραφή παραγωγού
                $delete_license_sql = "DELETE FROM ADEIA WHERE ID_ADEIAS='$license_id'";
                if (!$conn->query($delete_license_sql)) {
                    echo "Σφάλμα διαγραφής άδειας: " . $conn->error;
                }
            } else {
                echo "Σφάλμα: " . $conn->error;
            }
        } else {
            echo "Δεν βρέθηκε παραγωγός με ID: $id";
        }
    }
    
    // Εισαγωγή Προϊόντος
    if (isset($_POST['add_product'])) {
        $id = $_POST['product_id'];
        $name = $_POST['product_name'];
        $unit = $_POST['product_unit'];
        $price = $_POST['product_price'];
        
        $sql = "INSERT INTO PROIONTA (ID_PROIONTOS, ONOMA_PROIONTOS, MONADA_METRISIS, TIMI) 
                VALUES ('$id', '$name', '$unit', '$price')";
        if (!$conn->query($sql)) {
            echo "Σφάλμα: " . $conn->error;
        }
    }
    
    // Διαγραφή Προϊόντος
    if (isset($_POST['delete_product'])) {
        $id = $_POST['delete_product_id'];
        $sql = "DELETE FROM PROIONTA WHERE ID_PROIONTOS='$id'";
        if (!$conn->query($sql)) {
            echo "Σφάλμα: " . $conn->error;
        }
    }
    
    // Εισαγωγή Παραλαβής
    if (isset($_POST['add_receipt'])) {
        $id = $_POST['receipt_id'];
        $quantity = $_POST['receipt_quantity'];
        $date = $_POST['receipt_date'];
        $producer_id = $_POST['producer_id_select'];
        $product_id = $_POST['product_id_select'];
        $method = $_POST['receipt_method'];
        
        $sql = "INSERT INTO PARALABI (ID_PARALABIS, POSOTITA, HMEROMINIA, ID_PARAGOGOU, ID_PROIONTOS, TROPOS_PARALABIS) 
                VALUES ('$id', '$quantity', '$date', '$producer_id', '$product_id', '$method')";
        if (!$conn->query($sql)) {
            echo "Σφάλμα: " . $conn->error;
        }
    }
    
    // Διαγραφή Παραλαβής
    if (isset($_POST['delete_receipt'])) {
        $id = $_POST['delete_receipt_id'];
        $sql = "DELETE FROM PARALABI WHERE ID_PARALABIS='$id'";
        if (!$conn->query($sql)) {
            echo "Σφάλμα: " . $conn->error;
        }
    }
}

// Μετακίνηση του κώδικα ανάκτησης δεδομένων ΜΕΤΑ την επεξεργασία των POST
// Αυτό εξασφαλίζει ότι θα εμφανίζονται τα πιο πρόσφατα δεδομένα

// Ανάκτηση δεδομένων για εμφάνιση
$producers = $conn->query("SELECT * FROM PARAGOGOS");
$products = $conn->query("SELECT * FROM PROIONTA");
$receipts = $conn->query("SELECT p.*, pr.ONOMATEPONIMO, pt.ONOMA_PROIONTOS 
                          FROM PARALABI p
                          JOIN PARAGOGOS pr ON p.ID_PARAGOGOU = pr.ID_PARAGOGOU
                          JOIN PROIONTA pt ON p.ID_PROIONTOS = pt.ID_PROIONTOS");
$licenses = $conn->query("SELECT * FROM ADEIA");
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Λαχαναγορά - Σύστημα Διαχείρισης</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2e7d32; /* Βαθύ Πράσινο */
            --primary-light: #43a047; /* Ανοιχτό Πράσινο */
            --accent-color: #ffd54f; /* Κίτρινο/Χρυσό για έμφαση */
            --bg-color: #f1f8e9; /* Πολύ απαλό πράσινο φόντο */
            --card-bg: #ffffff;
            --text-color: #37474f;
            --border-radius: 12px;
            --shadow: 0 8px 24px rgba(0,0,0,0.12);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--bg-color);
            background-image: linear-gradient(120deg, #f1f8e9 0%, #dcedc8 100%);
            color: var(--text-color);
            min-height: 100vh;
            padding: 40px 20px;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            animation: slideUp 0.8s ease-out;
        }

        @keyframes slideUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            padding: 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        header h1 {
            font-size: 2.8em;
            font-weight: 700;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }

        .subtitle {
            font-size: 1.1em;
            opacity: 0.9;
            font-weight: 300;
        }

        /* Tabs Styling */
        .tabs {
            display: flex;
            background: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 0 20px;
        }

        .tab {
            padding: 20px 30px;
            cursor: pointer;
            background: transparent;
            border: none;
            font-size: 1.1em;
            font-weight: 500;
            color: #78909c;
            position: relative;
            transition: var(--transition);
        }

        .tab:hover {
            color: var(--primary-color);
            background-color: rgba(46, 125, 50, 0.05);
        }

        .tab.active {
            color: var(--primary-color);
        }

        .tab.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--primary-color);
            border-radius: 4px 4px 0 0;
            animation: expandWidth 0.3s ease;
        }

        @keyframes expandWidth {
            from { width: 0; left: 50%; }
            to { width: 100%; left: 0; }
        }

        .content {
            padding: 40px;
            background: #fafafa;
        }

        .section {
            display: none;
        }

        .section.active {
            display: block;
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.98); }
            to { opacity: 1; transform: scale(1); }
        }

        /* Forms Styling */
        .grid-layout {
            display: grid;
            grid-template-columns: 1fr 2fr; /* Μικρή στήλη φόρμες, μεγάλη πίνακας */
            gap: 40px;
        }
        
        @media (max-width: 900px) {
            .grid-layout { grid-template-columns: 1fr; }
        }

        .forms-wrapper {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        .form-card {
            background: white;
            padding: 25px;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border-top: 5px solid var(--primary-color);
            transition: var(--transition);
        }

        .form-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .form-card.danger {
            border-top-color: #ef5350;
        }

        h2 {
            color: var(--primary-color);
            margin-bottom: 20px;
            font-size: 1.4em;
            display: flex;
            align-items: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #546e7a;
            font-size: 0.9em;
        }

        input, select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #eceff1;
            border-radius: 8px;
            font-size: 1em;
            transition: var(--transition);
            background: #f9fbe7;
        }

        input:focus, select:focus {
            outline: none;
            border-color: var(--primary-light);
            background: white;
            box-shadow: 0 0 0 4px rgba(67, 160, 71, 0.1);
        }

        .btn {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 14px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1em;
            font-weight: 600;
            width: 100%;
            transition: var(--transition);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn:hover {
            background: var(--primary-light);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(46, 125, 50, 0.3);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn-delete {
            background: #ef5350;
        }

        .btn-delete:hover {
            background: #e53935;
            box-shadow: 0 5px 15px rgba(229, 57, 53, 0.3);
        }

        /* Table Styling */
        .table-wrapper {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            padding: 20px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        th {
            background: var(--primary-color);
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.85em;
            letter-spacing: 1px;
        }

        th:first-child { border-top-left-radius: 8px; }
        th:last-child { border-top-right-radius: 8px; }

        td {
            padding: 15px;
            border-bottom: 1px solid #eceff1;
            color: #455a64;
        }

        tr:last-child td { border-bottom: none; }

        tr:nth-child(even) { background-color: #f1f8e9; }

        tr:hover {
            background-color: #e8f5e9;
            transform: scale(1.005);
            transition: transform 0.2s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            cursor: default;
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #cfd8dc; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #b0bec5; }

    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>🥬 Λαχαναγορά</h1>
            <div class="subtitle">Ολοκληρωμένο Σύστημα Διαχείρισης</div>
        </header>

        <div class="tabs">
            <button class="tab active" onclick="openTab('producers')">👨‍🌾 Παραγωγοί</button>
            <button class="tab" onclick="openTab('products')">🥕 Προϊόντα</button>
            <button class="tab" onclick="openTab('receipts')">📦 Παραλαβές</button>
        </div>

        <div class="content">
            <div id="producers" class="section active">
                <div class="grid-layout">
                    <div class="forms-wrapper">
                        <div class="form-card">
                            <h2>Προσθήκη Παραγωγού</h2>
                            <form method="POST">
                                <div class="form-group">
                                    <label>Κωδικός (ID)</label>
                                    <input type="text" name="producer_id" maxlength="4" placeholder="π.χ. P006" required>
                                </div>
                                <div class="form-group">
                                    <label>Ονοματεπώνυμο</label>
                                    <input type="text" name="producer_name" maxlength="40" placeholder="Όνομα Επώνυμο" required>
                                </div>
                                <div class="form-group">
                                    <label>Περιοχή</label>
                                    <input type="text" name="producer_region" maxlength="20" placeholder="π.χ. Αργολίδα" required>
                                </div>
                                <div class="form-group">
                                    <label>Κωδικός Άδειας</label>
                                    <input type="number" name="license_id" placeholder="π.χ. 6" required>
                                    <small style="color: #666; font-size: 0.8em;">Αν ο κωδικός δεν υπάρχει, θα δημιουργηθεί αυτόματα νέα άδεια</small>
                                </div>
                                <button type="submit" name="add_producer" class="btn">Αποθηκευση</button>
                            </form>
                        </div>

                        <div class="form-card danger">
                            <h2>Διαγραφή Παραγωγού & Άδειας</h2>
                            <form method="POST">
                                <div class="form-group">
                                    <label>Κωδικός προς διαγραφή</label>
                                    <input type="text" name="delete_producer_id" maxlength="4" placeholder="ID Παραγωγού" required>
                                    <small style="color: #666; font-size: 0.8em;">Θα διαγραφούν τόσο ο παραγωγός όσο και η άδειά του</small>
                                </div>
                                <button type="submit" name="delete_producer" class="btn btn-delete">Διαγραφη Παραγωγου & Αδειας</button>
                            </form>
                        </div>
                    </div>

                    <div class="table-wrapper">
                        <h2>Κατάλογος Παραγωγών</h2>
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Ονοματεπώνυμο</th>
                                    <th>Περιοχή</th>
                                    <th>Άδεια</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $producers->data_seek(0);
                                while($producer = $producers->fetch_assoc()): ?>
                                <tr>
                                    <td><strong><?php echo $producer['ID_PARAGOGOU']; ?></strong></td>
                                    <td><?php echo $producer['ONOMATEPONIMO']; ?></td>
                                    <td><?php echo $producer['PERIOXI']; ?></td>
                                    <td><span style="background:#e8f5e9; color:#2e7d32; padding:4px 8px; border-radius:4px; font-size:0.9em;"><?php echo $producer['ID_ADEIAS']; ?></span></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="products" class="section">
                <div class="grid-layout">
                    <div class="forms-wrapper">
                        <div class="form-card">
                            <h2>Προσθήκη Προϊόντος</h2>
                            <form method="POST">
                                <div class="form-group">
                                    <label>Κωδικός Προϊόντος</label>
                                    <input type="number" name="product_id" placeholder="π.χ. 50" required>
                                </div>
                                <div class="form-group">
                                    <label>Ονομασία</label>
                                    <input type="text" name="product_name" maxlength="30" placeholder="π.χ. Ντομάτες" required>
                                </div>
                                <div class="form-group">
                                    <label>Μονάδα Μέτρησης</label>
                                    <input type="text" name="product_unit" maxlength="3" placeholder="π.χ. kg" required>
                                </div>
                                <div class="form-group">
                                    <label>Τιμή (€)</label>
                                    <input type="number" step="0.01" name="product_price" placeholder="0.00" required>
                                </div>
                                <button type="submit" name="add_product" class="btn">Αποθηκευση</button>
                            </form>
                        </div>

                        <div class="form-card danger">
                            <h2>Διαγραφή</h2>
                            <form method="POST">
                                <div class="form-group">
                                    <label>Κωδικός προς διαγραφή</label>
                                    <input type="number" name="delete_product_id" placeholder="ID Προϊόντος" required>
                                </div>
                                <button type="submit" name="delete_product" class="btn btn-delete">Διαγραφη Προιοντος</button>
                            </form>
                        </div>
                    </div>

                    <div class="table-wrapper">
                        <h2>Κατάλογος Προϊόντων</h2>
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Ονομασία</th>
                                    <th>Μον.</th>
                                    <th>Τιμή</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $products->data_seek(0);
                                while($product = $products->fetch_assoc()): 
                                ?>
                                <tr>
                                    <td><strong><?php echo $product['ID_PROIONTOS']; ?></strong></td>
                                    <td><?php echo $product['ONOMA_PROIONTOS']; ?></td>
                                    <td><?php echo $product['MONADA_METRISIS']; ?></td>
                                    <td style="font-weight:bold; color:var(--primary-color);"><?php echo $product['TIMI']; ?> €</td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="receipts" class="section">
                <div class="grid-layout">
                    <div class="forms-wrapper">
                        <div class="form-card">
                            <h2>Νέα Παραλαβή</h2>
                            <form method="POST">
                                <div class="form-group">
                                    <label>Κωδικός Παραλαβής</label>
                                    <input type="number" name="receipt_id" required>
                                </div>
                                <div class="form-group">
                                    <label>Ποσότητα</label>
                                    <input type="number" step="0.01" name="receipt_quantity" required>
                                </div>
                                <div class="form-group">
                                    <label>Ημερομηνία</label>
                                    <input type="date" name="receipt_date" required>
                                </div>
                                <div class="form-group">
                                    <label>Παραγωγός</label>
                                    <select name="producer_id_select" required>
                                        <option value="">Επιλέξτε Παραγωγό</option>
                                        <?php 
                                        $producers->data_seek(0);
                                        while($producer = $producers->fetch_assoc()): 
                                        ?>
                                            <option value="<?php echo $producer['ID_PARAGOGOU']; ?>">
                                                <?php echo $producer['ID_PARAGOGOU']; ?> - <?php echo $producer['ONOMATEPONIMO']; ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Προϊόν</label>
                                    <select name="product_id_select" required>
                                        <option value="">Επιλέξτε Προϊόν</option>
                                        <?php 
                                        $products->data_seek(0);
                                        while($product = $products->fetch_assoc()): 
                                        ?>
                                            <option value="<?php echo $product['ID_PROIONTOS']; ?>">
                                                <?php echo $product['ONOMA_PROIONTOS']; ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Τρόπος Παραλαβής</label>
                                    <input type="text" name="receipt_method" maxlength="30" placeholder="π.χ. Φορτηγό" required>
                                </div>
                                <button type="submit" name="add_receipt" class="btn">Καταχωρηση</button>
                            </form>
                        </div>

                        <div class="form-card danger">
                            <h2>Διαγραφή</h2>
                            <form method="POST">
                                <div class="form-group">
                                    <label>Κωδικός προς διαγραφή</label>
                                    <input type="number" name="delete_receipt_id" required>
                                </div>
                                <button type="submit" name="delete_receipt" class="btn btn-delete">Διαγραφη Παραλαβης</button>
                            </form>
                        </div>
                    </div>

                    <div class="table-wrapper">
                        <h2>Ιστορικό Παραλαβών</h2>
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Ποσότητα</th>
                                    <th>Ημερομηνία</th>
                                    <th>Παραγωγός</th>
                                    <th>Προϊόν</th>
                                    <th>Τρόπος</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $receipts->data_seek(0);
                                while($receipt = $receipts->fetch_assoc()): ?>
                                <tr>
                                    <td><strong><?php echo $receipt['ID_PARALABIS']; ?></strong></td>
                                    <td style="color:#1976d2; font-weight:bold;"><?php echo $receipt['POSOTITA']; ?></td>
                                    <td><?php echo date("d/m/Y", strtotime($receipt['HMEROMINIA'])); ?></td>
                                    <td><?php echo $receipt['ONOMATEPONIMO']; ?></td>
                                    <td><?php echo $receipt['ONOMA_PROIONTOS']; ?></td>
                                    <td><?php echo $receipt['TROPOS_PARALABIS']; ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openTab(tabName) {
            // Απόκρυψη όλων των sections με animation
            var sections = document.getElementsByClassName('section');
            for (var i = 0; i < sections.length; i++) {
                sections[i].classList.remove('active');
            }

            // Απενεργοποίηση όλων των tabs
            var tabs = document.getElementsByClassName('tab');
            for (var i = 0; i < tabs.length; i++) {
                tabs[i].classList.remove('active');
            }

            // Εμφάνιση του επιλεγμένου section και ενεργοποίηση του tab
            // Χρησιμοποιούμε setTimeout για να επιτρέψουμε στο CSS animation να τρέξει ξανά
            setTimeout(() => {
                document.getElementById(tabName).classList.add('active');
            }, 50);
            
            // Βρες το κουμπί που πατήθηκε (χρησιμοποιώντας το event) ή μέσω query αν χρειαστεί
            // Εδώ απλοποιούμε υποθέτοντας ότι το event triggure-ρεται σωστά
            event.currentTarget.classList.add('active');
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>