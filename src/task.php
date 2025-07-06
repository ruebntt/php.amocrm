<?php
require_once __DIR__ . '/src/AmoCrmV4Client.php'; // подключение класса
require_once __DIR__ . '/config.php'; // конфиг

$amo = new AmoCrmV4Client(SUB_DOMAIN, CLIENT_ID, CLIENT_SECRET, CODE, REDIRECT_URL);

function moveDealsToWaiting($amo) {
    $pipeline_id = 1; 
    $status_id_zayavka = 100; 

    $deals = $amo->GETRequestApi("leads", [
        "filter[pipeline_id]" => $pipeline_id,
        "filter[status_id]" => $status_id_zayavka
    ]);

    if (isset($deals['_embedded']['leads'])) {
        foreach ($deals['_embedded']['leads'] as $deal) {
            $deal_id = $deal['id'];
            $deal_price = isset($deal['price']) ? $deal['price'] : 0;

            if ($deal_price > 5000) {
                $new_pipeline_id = 1; 
                $new_status_id = 200; 

                $update_response = $amo->POSTRequestApi("leads/$deal_id", [
                    "pipeline_id" => $new_pipeline_id,
                    "status_id" => $new_status_id
                ], "PATCH");
                echo "Перемещена сделка ID $deal_id на этап Ожидание клиента.\n";
            }
        }
    }
}

function cloneConfirmedDeal($amo) {
    $pipeline_id = 1; 
    $status_id_confirmed = 300; 

    $deals = $amo->GETRequestApi("leads", [
        "filter[pipeline_id]" => $pipeline_id,
        "filter[status_id]" => $status_id_confirmed
    ]);

    if (isset($deals['_embedded']['leads'])) {
        foreach ($deals['_embedded']['leads'] as $deal) {
            $deal_id = $deal['id'];
            $deal_price = isset($deal['price']) ? $deal['price'] : 0;

            if ($deal_price == 4999) {
                $notes = $amo->GETRequestApi("leads/$deal_id/notes");
                // Обратимся к задачам сделки
                $tasks = $amo->GETRequestApi("tasks", [
                    "filter[entity_id]" => $deal_id,
                    "filter[entity_type]" => "leads"
                ]);
                $new_deal = [
                    "name" => $deal['name'],
                    "pipeline_id" => $pipeline_id, 
                    "status_id" => 400, 
                    "price" => $deal_price,
                ];
                $new_deal_response = $amo->POSTRequestApi("leads", $new_deal, "POST");
                if ($new_deal_response && isset($new_deal_response['id'])) {
                    $new_deal_id = $new_deal_response['id'];
                    // Копируем примечания
                    if ($notes && isset($notes['_embedded']['notes'])) {
                        foreach ($notes['_embedded']['notes'] as $note) {
                            $new_note = [
                                "entity_id" => $new_deal_id,
                                "note_type" => $note['note_type'],
                                "text" => $note['text']
                            ];
                            $amo->POSTRequestApi("notes", $new_note, "POST");
                        }
                    }
                    if ($tasks && isset($tasks['_embedded']['tasks'])) {
                        foreach ($tasks['_embedded']['tasks'] as $task) {
                            $new_task = [
                                "entity_id" => $new_deal_id,
                                "entity_type" => "leads",
                                "text" => $task['text'],
                                "complete_till" => $task['complete_till'],
                                "task_type_id" => $task['task_type_id']
                            ];
                            $amo->POSTRequestApi("tasks", $new_task, "POST");
                        }
                    }
                    echo "Создана копия сделки ID $deal_id как ID $new_deal_id.\n";
                }
            }
        }
    }
}

// Вызов функций
moveDealsToWaiting($amo);
cloneConfirmedDeal($amo);
?>
