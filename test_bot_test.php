<?php

// заводим токен

	const TOKEN = '5881211467:AAEFE_9F0avuu2JV5naEdCrZ9eBG7BXxAv0';

// выражаем ссылку взаимодействия с ботом

	const BASE_URL = 'https://api.telegram.org/bot' . TOKEN . '/';
	
// заводим	функцию, прибавляющую методы и параметры

	function sendRequest($method, $params = [])
	{
		
	// проверям на наличие параметров
		
		if(!empty($params)) {
			$url = BASE_URL . $method . '?' . http_build_query($params);
		} else {
			$url = BASE_URL . $method;	
		}
		return json_decode(
			file_get_contents($url), 
			JSON_OBJECT_AS_ARRAY
		);
	}


// присвиваем переменной имформацию об обновлениях(что писали боту)
	$only_update_id = sendRequest('getUpdates');
	
// перебираем массив и берем только 'update_id', чтоб отвечал только на последнее обновление	
	foreach ($only_update_id['result'] as $offset) {
		$last_update = $offset['update_id'];
	}
	
// присваиваем переменной результаты обновлений использую параметр 'offset', чтоб передать данные только о последнем обновлении

	$updates = sendRequest('getUpdates', ['offset' => $last_update] );
	
// перебираем массив и в переменную message записываем текст сообщений, которые были отправлены боту

	foreach ($updates['result'] as $text) {
		$message =  $text['message']['text'];
		
	}
	
// осуществляем взаимодейстие с пользователем( отправка сообщений)

// первое сообщение, откликается на слово 'привет' , все таки бот вежливый
	if ($message == 'привет' or $message == 'Привет') {
		
		foreach ($updates['result'] as $update) {
			$chat_id = $update['message']['chat']['id'];
			sendRequest('sendMessage', ['chat_id' => $chat_id, 'text' => 'Привет! Я тоже рад тебя видеть) Давай перейдем к делу! ... 
			Не знаешь как?! напиши "помогите" и я все тебе объясню!)']);
			
		}
		
		// вывод инструкции и объяснение сути, 'ссылка' в ответе на сообщение 'привет'
		
	} elseif ( $message == 'помогите' or $message == 'Помогите') {
		
		foreach ($updates['result'] as $update) {
			$chat_id = $update['message']['chat']['id'];
			sendRequest('sendMessage', ['chat_id' => $chat_id, 'text' => 'Оу, ты обратился ко мне за помощью) Ну чтож, слушай: я покажу тебе какой день недели был любого числа, которое ты напишешь! Удобно да?! 
	Писать нужно в формате ГМД, а разделять слешами (/) например ГМД: 2023/02/25 или 
	ДМГ и разделять точкой (.) например ДМГ: 11/02/2015 на этом все, удачи!' ]);
			
		}	
		
		// сам функционал
		
	} else {
		
		// преобразование сообщения от пользователя в ( день недели, д/м/г)
		
	$otvet = strftime("%a, %d/%m/%Y", strtotime($message));
	
		// проверка на наличие букв и вывод сообщения с просьбой написать правильно
		
		if( preg_match("/[А-Яа-я]/", $message) ) {
			foreach ($updates['result'] as $update) {
				$chat_id = $update['message']['chat']['id'];
				sendRequest('sendMessage', ['chat_id' => $chat_id, 'text' => 'Введите, пожалуйста, дату в формате Д.М.Г или Г/М/Д! для помощи хорошенько крикните "помогите"!']);
				
			}
		} else {	
		
			//вывод преобразованного сообщения

			foreach ($updates['result'] as $update) {
				$chat_id = $update['message']['chat']['id'];
				sendRequest('sendMessage', ['chat_id' => $chat_id, 'text' => $otvet]);
				
			}
		}
	}