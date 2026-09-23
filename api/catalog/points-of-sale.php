<?php

use App\Database;
use App\Response;

require_once __DIR__ . '/../../vendor/autoload.php';

try {
    $pdo = Database::connection();

    $type = trim($_GET['type'] ?? '');
    $city = trim($_GET['city'] ?? '');
    $q = trim($_GET['q'] ?? '');

    $sql = "SELECT id, country_type, country_ar, country_en, city_ar, city_en, name_ar, name_en, branch_name_ar, branch_name_en, address_ar, address_en, phone, whatsapp, google_maps_url, is_main_distributor, display_order FROM points_of_sale WHERE is_active = 1";
    $params = [];

    if ($type === 'egypt' || $type === 'international') {
        $sql .= " AND country_type = :type";
        $params['type'] = $type;
    }

    if ($city !== '') {
        $sql .= " AND (city_ar = :city_ar OR city_en = :city_en)";
        $params['city_ar'] = $city;
        $params['city_en'] = $city;
    }

    if ($q !== '') {
        $sql .= " AND (name_ar LIKE :q1 OR name_en LIKE :q2 OR branch_name_ar LIKE :q3 OR branch_name_en LIKE :q4 OR address_ar LIKE :q5 OR address_en LIKE :q6 OR city_ar LIKE :q7 OR country_ar LIKE :q8)";
        $searchTerm = '%' . $q . '%';
        $params['q1'] = $searchTerm;
        $params['q2'] = $searchTerm;
        $params['q3'] = $searchTerm;
        $params['q4'] = $searchTerm;
        $params['q5'] = $searchTerm;
        $params['q6'] = $searchTerm;
        $params['q7'] = $searchTerm;
        $params['q8'] = $searchTerm;
    }

    $sql .= " ORDER BY display_order ASC, is_main_distributor DESC, city_ar ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    $egypt = [];
    $international = [];
    $citiesEgypt = [];
    $countriesIntl = [];

    foreach ($rows as $r) {
        if ($r['country_type'] === 'egypt') {
            $egypt[] = $r;
            $cAr = $r['city_ar'];
            $citiesEgypt[$cAr] = ($citiesEgypt[$cAr] ?? 0) + 1;
        } else {
            $international[] = $r;
            $cntAr = $r['country_ar'];
            $countriesIntl[$cntAr] = ($countriesIntl[$cntAr] ?? 0) + 1;
        }
    }

    Response::ok([
        'egypt' => $egypt,
        'international' => $international,
        'all' => $rows,
        'cities_egypt' => array_keys($citiesEgypt),
        'countries_international' => array_keys($countriesIntl),
        'total' => count($rows),
    ]);
} catch (\Throwable $e) {
    Response::serverError($e->getMessage());
}
