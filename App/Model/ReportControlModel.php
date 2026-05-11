<?php

namespace App\Model;


use App\Model\BaseModel;
use PDO;

use App\Helper\Date;

class ReportControlModel extends BaseModel
{
    protected $db;
    protected $control_table = 'sqlsonkullanmatarihi';
    protected $validity_table = 'sqlvalidity_date';

    /**
     * @param PDO|null $db Optional explicit PDO injection (recommended for scripts).
     */
    public function __construct(?PDO $db = null)
    {
        // Prefer explicit injection
        if ($db instanceof PDO) {
            $this->db = $db;
            return;
        }

        // Backwards-compat: try global $ac (defined by configs/config.php or bootstrap.php)
        global $ac;
        if (isset($ac) && $ac instanceof PDO) {
            $this->db = $ac;
            return;
        }

        // If called from standalone scripts that forgot bootstrap, load it.
        // This will set global $ac.
        $bootstrapPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'bootstrap.php';
        if (is_file($bootstrapPath)) {
            require_once $bootstrapPath;
        } else {
            // Fallback (in case bootstrap.php isn't present or path differs)
            $configPath = $_SERVER['DOCUMENT_ROOT'] . '/configs/config.php';
            if (!empty($_SERVER['DOCUMENT_ROOT']) && is_file($configPath)) {
                require_once $configPath;
            }
        }

        global $ac;
        $this->db = (isset($ac) && $ac instanceof PDO) ? $ac : null;
    }

    public function getReportFillingList($month, $year)
    {
        $query = "SELECT report_id,report_number, firma_adi,bulundugu_bolge,cihaz_no, ay, yil FROM $this->control_table";
        $conditions = [];
        $params = [];

        if (!empty($year)) {
            $conditions[] = 'yil = ?';
            $params[] = $year;
        }

        if (!empty($month)) {
            $conditions[] = 'ay = ?';
            $params[] = Date::setMonth($month);
        }

        if (!empty($conditions)) {
            $query .= ' WHERE ' . implode(' AND ', $conditions);
        }

        //$query .= ' GROUP BY firma_adi';

        $sql = $this->db->prepare($query);
        $sql->execute($params);
        return $sql->fetchAll(PDO::FETCH_OBJ);
    }

    public function getReportControlList($month, $year)
    {
        $query = "SELECT report_id,report_number, firma_adi,validity_date, ay, yil FROM $this->validity_table";
        $conditions = [];
        $params = [];

        if (!empty($year)) {
            $conditions[] = 'yil = ?';
            $params[] = $year;
        }

        if (!empty($month)) {
            $conditions[] = 'ay = ?';
            $params[] = Date::setMonth($month);
        }

        if (!empty($conditions)) {
            $query .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $query .= ' GROUP BY firma_adi';

        $sql = $this->db->prepare($query);
        $sql->execute($params);
        return $sql->fetchAll(PDO::FETCH_OBJ);
    }
}
