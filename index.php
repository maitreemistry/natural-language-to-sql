<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQL Query Input</title>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: linear-gradient(to right, #fbc2eb, #a6c1ee);
            color: white;
            flex-direction: column;
        }

        .container {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            width: 350px;
            text-align: center;
        }

        h2 {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            font-size: 14px;
        }

        input {
            width: 90%;
            padding: 10px;
            margin-bottom: 15px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
        }

        .btn {
            background: #ff8c00;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: background 0.3s;
        }

        .btn:hover {
            background: #ff6500;
        }

        #result {
            margin-top: 20px;
            background: white;
            color: black;
            padding: 10px;
            border-radius: 5px;
            width: 80%;
            text-align: center;
        }
    </style>

    <script>
        function nlToSql() {
            let nlQuery = document.getElementById("query").value.toLowerCase().trim();
            let sqlQuery = "Query not recognized!";
            let tablename = document.getElementById("table").value.toLowerCase().trim();
            let col1 = document.getElementById("col1").value.toLowerCase().trim();
            let col2 = document.getElementById("col2").value.toLowerCase().trim();
            
            if (!tablename) {
                swal("Error", "Please enter a table name", "error");
                return;
            }
        
            // Common synonyms for SELECT queries
            let selectKeywords = ["select", "project", "get", "fetch", "retrieve", "show", "display", "list", "give","choose","take"];
            let allKeywords = ["all", "everything", "every row", "each row", "entire data", "complete data","complete","full","whole"];
            let whereKeywords = ["where", "such that", "whose", "filter by", "condition", "with"];
            
            // Handling SELECT * queries
            if (selectKeywords.some(word => nlQuery.includes(word)) && allKeywords.some(word => nlQuery.includes(word)) && !whereKeywords.some(word => nlQuery.includes(word))) {
                sqlQuery = `SELECT * FROM ${tablename};`;
            }
        
            // Handling specific column selection
            if (selectKeywords.some(word => nlQuery.includes(word))) {
                if (nlQuery.includes(col1) && !whereKeywords.some(word => nlQuery.includes(word))) {
                    sqlQuery = `SELECT ${col1} FROM ${tablename};`;
                } 
                if (nlQuery.includes(col2) && !whereKeywords.some(word => nlQuery.includes(word))) {
                    sqlQuery = `SELECT ${col2} FROM ${tablename};`;
                }
            }
            
            // Handling conditions (>, <, =, !=)
            let greaterKeywords = ["greater than", ">", "more than", "above", "higher than", "exceeds"];
            let lessKeywords = ["less than", "<", "below", "lower than", "under"];
            let equalKeywords = ["equal to", "=", "equals", "exactly", "same as", "equal"];
            let notEqualKeywords = ["not equal to", "!=", "different from", "not the same as"];
            
            let value = nlQuery.match(/\d+/) ? nlQuery.match(/\d+/)[0] : null;
            
            if (value && whereKeywords.some(word => nlQuery.includes(word))) {
                if (greaterKeywords.some(word => nlQuery.includes(word))) {
                    if (nlQuery.includes(col1)) {
                        sqlQuery = `SELECT * FROM ${tablename} WHERE ${col1} > ${value};`;
                    } else if (nlQuery.includes(col2)) {
                        sqlQuery = `SELECT * FROM ${tablename} WHERE ${col2} > ${value};`;
                    }
                }
                if (lessKeywords.some(word => nlQuery.includes(word))) {
                    if (nlQuery.includes(col1)) {
                        sqlQuery = `SELECT * FROM ${tablename} WHERE ${col1} < ${value};`;
                    } else if (nlQuery.includes(col2)) {
                        sqlQuery = `SELECT * FROM ${tablename} WHERE ${col2} < ${value};`;
                    }
                }
                if (equalKeywords.some(word => nlQuery.includes(word))) {
                    if (nlQuery.includes(col1)) {
                        sqlQuery = `SELECT * FROM ${tablename} WHERE ${col1} = ${value};`;
                    } else if (nlQuery.includes(col2)) {
                        sqlQuery = `SELECT * FROM ${tablename} WHERE ${col2} = ${value};`;
                    }
                }
                if (notEqualKeywords.some(word => nlQuery.includes(word))) {
                    if (nlQuery.includes(col1)) {
                        sqlQuery = `SELECT * FROM ${tablename} WHERE ${col1} != ${value};`;
                    } else if (nlQuery.includes(col2)) {
                        sqlQuery = `SELECT * FROM ${tablename} WHERE ${col2} != ${value};`;
                    }
                }
            }
            
            // Handling COUNT queries
            let countKeywords = ["count", "how many", "total number of", "number of rows", "total"];
            if (countKeywords.some(word => nlQuery.includes(word))) {
                sqlQuery = `SELECT COUNT(*) FROM ${tablename};`;
            }

            swal("Generated SQL Query:", sqlQuery).then(() => {
                executeQuery(sqlQuery);
            });
        }

        function executeQuery(sqlQuery) {
            $.ajax({
                url: "query_executor.php",
                type: "POST",
                data: { sql: sqlQuery },
                success: function(response) {
                    $("#result").html(response);
                },
                error: function() {
                    $("#result").html("<p style='color:red;'>Error executing query.</p>");
                }
            });
        }
    </script>

</head>
<body>
    <div class="container">
        <h2>SQL Query Input</h2>
        <!-- <label for="table">Table Name:</label>
        <input type="text" id="table" placeholder="Enter table name">
        <label for="col1">Column 1 Name:</label>
        <input type="text" id="col1" placeholder="Enter column name">
        <label for="col2">Column 2 Name:</label>
        <input type="text" id="col2" placeholder="Enter column name"> -->

<label for="table">Table Name:</label>
<input list="tables" id="table" name="table">
<datalist id="tables">
    <option value="Student">
    <option value="Employee">
    <option value="Product">
    <option value="Course">
    <option value="Event">
</datalist>

<label for="col1">Column 1 Name:</label>
<input list="columns" id="col1" name="col1">
<datalist id="columns">
    <option value="roll_no">Roll No</option>
    <option value="age">Age</option>
    <option value="emp_id">Employee ID</option>
    <option value="salary">Salary</option>
    <option value="product_id">Product ID</option>
    <option value="price">Price</option>
    <option value="course_id">Course ID</option>
    <option value="duration">Duration</option>
    <option value="event_id">Event ID</option>
    <option value="attendees">Attendees</option>
</datalist>
<label for="col2">Column 2 Name:</label>
<input list="columns" id="col2" name="col2">
<datalist id="columns">
    <option value="roll_no">Roll No</option>
    <option value="age">Age</option>
    <option value="emp_id">Employee ID</option>
    <option value="salary">Salary</option>
    <option value="product_id">Product ID</option>
    <option value="price">Price</option>
    <option value="course_id">Course ID</option>
    <option value="duration">Duration</option>
    <option value="event_id">Event ID</option>
    <option value="attendees">Attendees</option>
</datalist>

        <label for="query">Natural Language Query:</label>
        <input type="text" id="query" placeholder="Enter query in English">
        <button class="btn" onclick="nlToSql()">Submit</button>
    </div>
    <div id="result"></div>
</body>
</html>
