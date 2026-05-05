<?php
/**
 * dropdown_helper.php
 * MySQLi-based dropdown/option generator
 * 
 * Provides helper functions to safely fetch dropdown options
 * using prepared statements
 */

/**
 * Get options from database using prepared statement
 * 
 * @param mysqli $conn - MySQLi connection object
 * @param string $table - Table name
 * @param string $id_field - Primary key field name
 * @param string $name_field - Display field name
 * @param array $where - Optional WHERE conditions ['column' => 'value']
 * @return array Array of rows with [id_field, name_field]
 */
function getOptions($conn, $table, $id_field, $name_field, $where = [])
{
    $options = [];
    
    try {
        // Build query
        $query = "SELECT $id_field, $name_field FROM $table";
        
        // Add WHERE conditions if provided
        if (!empty($where)) {
            $conditions = [];
            foreach ($where as $col => $val) {
                $conditions[] = "$col = ?";
            }
            $query .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $query .= " ORDER BY $name_field ASC";
        
        // Prepare and execute
        $stmt = mysqli_prepare($conn, $query);
        
        if (!$stmt) {
            error_log("Prepare Error in getOptions: " . mysqli_error($conn));
            return $options;
        }
        
        // Bind WHERE parameters if any
        if (!empty($where)) {
            $types = '';
            $values = [];
            foreach ($where as $col => $val) {
                $types .= 's';  // Assume string type
                $values[] = $val;
            }
            mysqli_stmt_bind_param($stmt, $types, ...$values);
        }
        
        // Execute
        if (!mysqli_stmt_execute($stmt)) {
            error_log("Execute Error in getOptions: " . mysqli_error($conn));
            return $options;
        }
        
        // Get result
        $result = mysqli_stmt_get_result($stmt);
        
        while ($row = mysqli_fetch_assoc($result)) {
            $options[] = $row;
        }
        
        mysqli_stmt_close($stmt);
        
    } catch (Exception $e) {
        error_log("Exception in getOptions: " . $e->getMessage());
    }
    
    return $options;
}

/**
 * Format options for rendering in select HTML
 * Converts database rows to standardized format
 * 
 * @param array $data - Array of rows from getOptions()
 * @param string $id_field - Primary key field name
 * @param string $name_field - Display field name
 * @param string $format - Format pattern: 'id_name' (default) or 'name'
 * @return array Array of ['id' => value, 'label' => display_text]
 */
function formatOptions($data, $id_field, $name_field, $format = 'id_name')
{
    $formatted = [];
    
    foreach ($data as $row) {
        $id = $row[$id_field] ?? '';
        $name = $row[$name_field] ?? '';
        
        // Format label based on pattern
        $label = match($format) {
            'name' => $name,
            'id_name' => "$id - $name",
            default => "$id - $name"
        };
        
        $formatted[] = [
            'id' => $id,
            'label' => $label
        ];
    }
    
    return $formatted;
}

/**
 * Render HTML select options from formatted data
 * 
 * @param array $options - Formatted options from formatOptions()
 * @param mixed $selected - Currently selected value (default null)
 * @param string $placeholder - Placeholder option text
 * @return string HTML option tags
 */
function renderOptions($options, $selected = null, $placeholder = '-- Pilih --')
{
    $html = '';
    
    if ($placeholder) {
        $html .= '<option value="">'.htmlspecialchars($placeholder).'</option>';
    }
    
    foreach ($options as $opt) {
        $isSelected = ($selected == $opt['id']) ? 'selected' : '';
        $html .= '<option value="'.htmlspecialchars($opt['id']).'" '.$isSelected.'>';
        $html .= htmlspecialchars($opt['label']);
        $html .= '</option>';
    }
    
    return $html;
}

/**
 * Get single option value by ID
 * Useful for displaying current value in forms
 * 
 * @param mysqli $conn - MySQLi connection
 * @param string $table - Table name
 * @param string $id_field - Primary key field
 * @param string $name_field - Display field
 * @param mixed $id - ID to lookup
 * @return string|null Display text or null if not found
 */
function getOptionLabel($conn, $table, $id_field, $name_field, $id)
{
    $query = "SELECT $name_field FROM $table WHERE $id_field = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $query);
    
    if (!$stmt) {
        error_log("Prepare Error in getOptionLabel: " . mysqli_error($conn));
        return null;
    }
    
    mysqli_stmt_bind_param($stmt, 's', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        mysqli_stmt_close($stmt);
        return $row[$name_field];
    }
    
    mysqli_stmt_close($stmt);
    return null;
}
