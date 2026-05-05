<?php
/**
 * dropdown_helper.php
 * Helper functions untuk mengambil data dropdown dari database (PDO)
 */

/**
 * Ambil semua baris dari tabel sebagai array asosiatif.
 *
 * @param PDO    $conn       Koneksi PDO
 * @param string $table      Nama tabel
 * @param string $id_field   Nama kolom ID
 * @param string $name_field Nama kolom label/nama
 * @return array
 */
function getOptions(PDO $conn, string $table, string $id_field, string $name_field): array
{
    try {
        $query = "SELECT $id_field, $name_field FROM $table";
        $stmt  = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Ambil semua baris dari tabel dan format label sebagai "id - name".
 *
 * @param PDO    $conn       Koneksi PDO
 * @param string $table      Nama tabel
 * @param string $id_field   Nama kolom ID
 * @param string $name_field Nama kolom label/nama
 * @return array  Array berisi ['id' => ..., 'label' => 'id - name']
 */
function getOptionsWithFormat(PDO $conn, string $table, string $id_field, string $name_field): array
{
    try {
        $query = "SELECT $id_field, $name_field FROM $table";
        $stmt  = $conn->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $options = [];
        foreach ($results as $row) {
            $options[] = [
                'id'    => $row[$id_field],
                'label' => $row[$id_field] . ' - ' . $row[$name_field],
            ];
        }
        return $options;
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Render HTML <option> tags dari array yang dikembalikan getOptionsWithFormat().
 * Otomatis menandai opsi yang dipilih (selected).
 *
 * @param array  $options     Array dari getOptionsWithFormat()
 * @param mixed  $selected    Nilai yang sedang dipilih
 * @param string $placeholder Teks opsi default (value="0")
 * @return string HTML string dari semua <option>
 */
function renderOptions(array $options, mixed $selected = 0, string $placeholder = '-- Pilih --'): string
{
    $html = '<option value="0">' . htmlspecialchars($placeholder, ENT_QUOTES, 'UTF-8') . '</option>';
    foreach ($options as $opt) {
        $sel   = ((string)$opt['id'] === (string)$selected) ? ' selected' : '';
        $html .= '<option value="' . htmlspecialchars((string)$opt['id'], ENT_QUOTES, 'UTF-8') . '"' . $sel . '>'
               . htmlspecialchars($opt['label'], ENT_QUOTES, 'UTF-8')
               . '</option>';
    }
    return $html;
}
