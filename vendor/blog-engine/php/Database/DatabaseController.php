<?php
namespace BlogEngine\Database {
    use BlogEngine\Configuration;
    use BlogEngine\Database\Tables;
    use PHPMailer\PHPMailer\PHPMailer;

    class DatabaseController {
        public function Connection(){
            $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

            // Jeżeli połączenie nie przejdzie pomyślnie, wyrzuć błąd
            if(!$conn){
                error_log("Błąd połączenia z bazą: " . mysqli_connect_error());
            }

            return $conn;
        }

        public function Close($conn) { mysqli_close($conn); }

        function Insert($into, $values) : bool{
            $conn = $this->Connection();
            if(mysqli_query($conn, "INSERT INTO {$into} VALUES({$values})")){
                $this->Close($conn);
                return true;
            } else {
                $this->Close($conn);
                return false;
            }
        }

        function CreateTable($name, $columns){
            $conn = $this->Connection();
            if(mysqli_query($conn, "CREATE TABLE IF NOT EXISTS {$name}({$columns});")){

            } else {
                echo "Utworzenie tabeli {$name} nie przeszło pomyślnie!";
            }
            $this->Close($conn);
        }

        function CreateDatabase(){
            $srv = $this->Connection();

            // Ładowanie struktury bazy
            if($configs = scandir(__DIR__."/Tables")){
                if(sizeof($configs) > 0){
                    foreach($configs as $config){
                        if(pathinfo($config, PATHINFO_EXTENSION) == "json"){
                            $tables = array();
                            $table_content = file_get_contents(__DIR__."/Tables/" . $config);
                            $structure = json_decode($table_content, true);

                            foreach($structure as $name=>$table){
                                $query = "";
                                foreach ($table as $column){
                                    $column_data = [
                                        "column_name" => "", "datatype" => "", "length" => 0, "enum_data" => [],
                                        "primary_key" => false, "unique" => false,
                                        "auto_increment" => false, "not_null" => false, "default" => ["value" => "", "is_string" => false], "foreign_key" => ["references" => "", "column" => "", "column_name" => ""]
                                    ];

                                    if(array_key_exists("column_name", $column))
                                        $column_data['column_name'] = $column['column_name'];

                                    if(array_key_exists("datatype", $column))
                                        $column_data['datatype'] = $column['datatype'];

                                    if(array_key_exists("length", $column))
                                        $column_data['length'] = $column['length'];

                                    if(array_key_exists("enum_data", $column))
                                        $column_data['enum_data'] = $column['enum_data'];

                                    if(array_key_exists("primary_key", $column))
                                        $column_data['primary_key'] = $column['primary_key'];

                                    if(array_key_exists("foreign_key", $column)){
                                        $column_data['foreign_key']['references'] = $column['foreign_key']['references'];
                                        $column_data['foreign_key']['column'] = $column['foreign_key']['column'];
                                        $column_data['foreign_key']['column_name'] = $column['column_name'];
                                    }

                                    if(array_key_exists("auto_increment", $column))
                                        $column_data['auto_increment'] = $column['auto_increment'];

                                    if(array_key_exists("not_null", $column))
                                        $column_data['not_null'] = $column['not_null'];

                                    if(array_key_exists("unique", $column))
                                        $column_data['unique'] = $column['unique'];

                                    if(array_key_exists("default", $column)) {
                                        $column_data['default']['value'] = $column['default']['value'];
                                        $column_data['default']['is_string'] = $column['default']['is_string'];
                                    }

                                    foreach ($column_data as $key=>$new_column){
                                        if($key == "foreign_key") {
                                            if($new_column['references'] != "" && $new_column['column'] != "")
                                                $query .= ", FOREIGN KEY ({$new_column['column_name']}) REFERENCES {$new_column['references']}({$new_column['column']}) ";
                                        } else if($key == "default"){
                                            if($new_column['value'] != "") {
                                                if ($new_column['is_string'])
                                                    $query .= "DEFAULT \"{$new_column['value']}\" ";
                                                else
                                                    $query .= "DEFAULT {$new_column['value']} ";
                                            }
                                        } else {
                                            if(gettype($new_column) == "string"){
                                                if($new_column != ""){
                                                    $query .= $new_column . " ";
                                                }
                                            } else if(gettype($new_column) == "integer"){
                                                if($new_column != 0){
                                                    $query .= "({$new_column}) ";
                                                }
                                            } else if(gettype($new_column) == "boolean"){
                                                if($new_column){
                                                    $query .= strtoupper(($key != "auto_increment"?str_replace("_", " ", $key):$key)) . " ";
                                                }
                                            } else if(gettype($new_column) == "array"){
                                                if(sizeof($new_column) > 0){
                                                    $elements = "";
                                                    foreach ($new_column as $element){
                                                        $elements .= "'{$element}', ";
                                                    }
                                                    $elements = substr($elements, 0, strlen($elements) - 2);
                                                    $query .= "({$elements}) ";
                                                }
                                            }
                                        }
                                    }
                                    $query .= ", ";
                                }
                                $query = substr($query, 0, strlen($query) - 3);
                                array_push($tables, [
                                    "Nazwa" => $name,
                                    "Struktura" => $query
                                ]);
                            }

                            // Dodawanie do bazy tabel
                            foreach ($tables as $table){
                                $this->CreateTable($table['Nazwa'], $table['Struktura']);
                            }

                            // Dodawanie do tabeli "users" administratora
                            $this->Insert("users", "1, NULL, '', NULL, '{$_POST['ADMIN_USER']}', '{$_POST['BLOG_AUTHOR']}', PASSWORD('{$_POST['ADMIN_PASS']}'), '{$_POST['ADMIN_EMAIL']}', 'administrator', UUID(), NOW(), NULL");

                            // Dodawanie do bazy domyślnych danych
                            if($data = scandir(__DIR__ . "/Values")) {
                                if(sizeof($data) > 0){
                                    foreach ($data as $datum){
                                        if(pathinfo($datum, PATHINFO_EXTENSION) == "json"){
                                            $value_content = file_get_contents(__DIR__ . "/Values/" . $datum);
                                            $value_structure = json_decode($value_content, true);
                                            //print_r($value_structure);
                                            $i = 0;
                                            foreach ($value_structure as $table) {
                                                foreach ($table as $row=>$columns){
                                                    $to_table = array_keys($value_structure)[$i];
                                                    $insert_columns = "";
                                                    $insert_values = "";
                                                    foreach ($columns as $column=>$value){
                                                        $insert_columns .= $column . ", ";
                                                        if($value == "NULL")
                                                            $insert_values .= "NULL, ";
                                                        else if($value == "NOW()")
                                                            $insert_values .= "NOW(), ";
                                                        else if(is_string($value))
                                                            $insert_values .= "\"$value\", ";
                                                        else if(is_int($value) || is_float($value) || is_double($value))
                                                            $insert_values .= "$value, ";
                                                        else if(is_bool($value)){
                                                            if($value)
                                                                $insert_values .= "true, ";
                                                            else
                                                                $insert_values .= "false, ";
                                                        }
                                                    }   
                                                    $insert_columns = trim($insert_columns, ", ");
                                                    $insert_values = trim($insert_values, ", ");
                                                    $query = "INSERT INTO $to_table($insert_columns) VALUES ($insert_values);";
                                                    mysqli_query($srv, $query);
                                                }
                                                $i++;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                } else {
                    error_log("Folder \"Tables\" jest pusty. Zatrzymywanie tworzenia bazy!");
                }
            } else {
                error_log("Nie ma dostępnego folderu \"Tables\". Zatrzymywanie tworzenia bazy!");
            }
        }

        function Log($content, $log_level = 'Information', $id_user = null){
            $ip_address = file_get_contents("https://api.ipify.org");
            $this->Insert("logs(ip_address, content, log_level, created_by)", "'$ip_address', '$content', '$log_level', $id_user");
        }
    }
}