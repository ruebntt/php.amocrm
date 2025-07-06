<?php
require_once __DIR__ . '/src/AmoCrmV4Client.php';

define('SUB_DOMAIN', 'elizafgt');
define('CLIENT_ID', 'd976f64c-ce4a-4bd9-a333-745db1ae3b57');
define('CLIENT_SECRET', 'cwrL9Z7hRKYJYI82Ayk4rW0JGmMAks0iuuWIQ9oAMV0wV71eB8PPyEOCQ2xBaCrB');
define('CODE', 'def50200050b1db9d7fd5b4f8b82861af3810947ff45b9ed3ab333107b7fa98b379714fdcf01e62dc38c58c5996c4397b5210a349b3107450b4182c3766274c7653717d9823bcb8b5f0985f1c364364bc84e155749a5edfb9409c3cf29048ea165c04c94743fd1cd744ce013d0e699d15512ae210de0564244fea79cd241538a05d518a235097565f1e7c65ba9216f4210538d25ec08d667bec4c0e0e740429d9f0400f7592e833323d33da021b45286048ea5b4dfcc2eba3f01c407bd77f81a74d6318485582c05aea745f615ca98309de00e13a52b6c76958174a1b4fa769fdfe85d59e0840152f22a9f90f355875cd7fa49946bd2c631599fd6b47df23ffbcd2210959223edd6f809eb5795ec83b2d42f835eea00352c3c50b5e0454ca801da972b447bf96ab99a4bf029e5ef992c9ae376d19e72b0c5edc6b098209cd4a72c96ba88000a884fff987d1319d887ae751d12338e5fce2c51964ebbe5956aa259fc54b62fd04f63e064cd53bac2715b6d73d8c735d29c47053f6ac4dde4b854c0fc0626a22e0b43c6a1c0ea236760e6ec7bda430099871a253e3f4180c81a4760e757197976e61f916a27cad9ec8ade0c983e2f28986c9bb83f9ba630b909885cb603ba2c737b70618205f2f36443ce3e2a8bdd7fb26c1e63b2070e800208740e4cc20a7702f3eb1fa7a6ea609f804835f413526e80063160b506fbbf515d3efa848e2556a1f100');
define('REDIRECT_URL', 'https://elizafgt.amocrm.ru');

echo "<pre>";

try {
    $amoV4Client = new AmoCrmV4Client(SUB_DOMAIN, CLIENT_ID, CLIENT_SECRET, CODE, REDIRECT_URL);
    $leads = $amoV4Client->GETRequestApi("leads", [
        "filter[statuses][0][pipeline_id]" => 363,
        "filter[statuses][0][status_id]"   => 79140950,
        "filter[statuses][1][pipeline_id]" => 367,
        "filter[statuses][1][status_id]"   => 78140958,
        "filter[price] => 5000"
    ]);
    $leads = $amoV4Client->POSTRequestApi("leads",[параметры],"PATCH");
    
    $leads_client_confirm = $amoV4Client->GETAll("leads");

    var_dump($leads);
    
}

catch (Exception $ex) {
    var_dump($ex);
    file_put_contents("ERROR_LOG.txt", 'Ошибка: ' . $ex->getMessage() . PHP_EOL . 'Код ошибки:' . $ex->getCode());
}
