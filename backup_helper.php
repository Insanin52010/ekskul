<?php
if (!function_exists('exportDatabaseKeFile')) {
    function exportDatabaseKeFile($conn, $filePath = __DIR__ . '/database.sql') {
        $tables = [];
        $res = mysqli_query($conn, "SHOW TABLES");
        if (!$res) return false;

        while ($row = mysqli_fetch_row($res)) {
            $tables[] = $row[0];
        }

        $sqlScript = "-- Auto-Backup Database Portal Ekskul SMKN 5 Malang\n";
        $sqlScript .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            $createRes = mysqli_query($conn, "SHOW CREATE TABLE `$table`");
            $createRow = mysqli_fetch_row($createRes);
            $sqlScript .= "DROP TABLE IF EXISTS `$table`;\n";
            $sqlScript .= $createRow[1] . ";\n\n";

            $dataRes = mysqli_query($conn, "SELECT * FROM `$table`");
            $numFields = mysqli_num_fields($dataRes);

            while ($row = mysqli_fetch_row($dataRes)) {
                $sqlScript .= "INSERT INTO `$table` VALUES(";
                for ($j = 0; $j < $numFields; $j++) {
                    if (is_null($row[$j])) {
                        $sqlScript .= "NULL";
                    } else {
                        $escaped = mysqli_real_escape_string($conn, $row[$j]);
                        $sqlScript .= "'" . $escaped . "'";
                    }
                    if ($j < ($numFields - 1)) {
                        $sqlScript .= ", ";
                    }
                }
                $sqlScript .= ");\n";
            }
            $sqlScript .= "\n";
        }

        $sqlScript .= "SET FOREIGN_KEY_CHECKS=1;\n";
        return file_put_contents($filePath, $sqlScript) !== false;
    }
}
?>