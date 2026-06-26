<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Сообщения валидации
    |--------------------------------------------------------------------------
    */

    'accepted' => 'Поле :attribute должно быть принято.',
    'active_url' => 'Поле :attribute не является допустимым URL-адресом.',
    'after' => 'Поле :attribute должно быть датой после :date.',
    'after_or_equal' => 'Поле :attribute должно быть датой не раньше :date.',
    'alpha' => 'Поле :attribute должно содержать только буквы.',
    'alpha_dash' => 'Поле :attribute должно содержать только буквы, цифры, дефисы и подчёркивания.',
    'alpha_num' => 'Поле :attribute должно содержать только буквы и цифры.',
    'array' => 'Поле :attribute должно быть массивом.',
    'before' => 'Поле :attribute должно быть датой до :date.',
    'before_or_equal' => 'Поле :attribute должно быть датой не позже :date.',
    'between' => [
        'numeric' => 'Поле :attribute должно быть от :min до :max.',
        'file' => 'Размер файла :attribute должен быть от :min до :max кб.',
        'string' => 'Длина :attribute должна быть от :min до :max символов.',
        'array' => 'Поле :attribute должно содержать от :min до :max элементов.',
    ],
    'boolean' => 'Поле :attribute должно быть истиной или ложью.',
    'confirmed' => 'Подтверждение поля :attribute не совпадает.',
    'date' => 'Поле :attribute не является допустимой датой.',
    'date_equals' => 'Поле :attribute должно быть датой :date.',
    'date_format' => 'Поле :attribute не соответствует формату :format.',
    'different' => 'Поля :attribute и :other должны различаться.',
    'digits' => 'Поле :attribute должно содержать :digits цифр.',
    'digits_between' => 'Поле :attribute должно содержать от :min до :max цифр.',
    'dimensions' => 'Изображение :attribute имеет недопустимые размеры.',
    'distinct' => 'Поле :attribute содержит повторяющееся значение.',
    'email' => 'Поле :attribute должно быть действительным адресом электронной почты.',
    'ends_with' => 'Поле :attribute должно оканчиваться одним из следующих значений: :values.',
    'exists' => 'Выбранное значение для :attribute недействительно.',
    'file' => 'Поле :attribute должно быть файлом.',
    'filled' => 'Поле :attribute не должно быть пустым.',
    'gt' => [
        'numeric' => 'Поле :attribute должно быть больше :value.',
        'file' => 'Размер файла :attribute должен быть больше :value кб.',
        'string' => 'Длина :attribute должна быть больше :value символов.',
        'array' => 'Поле :attribute должно содержать больше :value элементов.',
    ],
    'gte' => [
        'numeric' => 'Поле :attribute должно быть не меньше :value.',
        'file' => 'Размер файла :attribute должен быть не меньше :value кб.',
        'string' => 'Длина :attribute должна быть не меньше :value символов.',
        'array' => 'Поле :attribute должно содержать не меньше :value элементов.',
    ],
    'image' => 'Поле :attribute должно быть изображением.',
    'in' => 'Выбранное значение для :attribute недействительно.',
    'in_array' => 'Поле :attribute не существует в :other.',
    'integer' => 'Поле :attribute должно быть целым числом.',
    'ip' => 'Поле :attribute должно быть допустимым IP-адресом.',
    'ipv4' => 'Поле :attribute должно быть допустимым IPv4-адресом.',
    'ipv6' => 'Поле :attribute должно быть допустимым IPv6-адресом.',
    'json' => 'Поле :attribute должно быть допустимой JSON-строкой.',
    'lt' => [
        'numeric' => 'Поле :attribute должно быть меньше :value.',
        'file' => 'Размер файла :attribute должен быть меньше :value кб.',
        'string' => 'Длина :attribute должна быть меньше :value символов.',
        'array' => 'Поле :attribute должно содержать меньше :value элементов.',
    ],
    'lte' => [
        'numeric' => 'Поле :attribute должно быть не больше :value.',
        'file' => 'Размер файла :attribute должен быть не больше :value кб.',
        'string' => 'Длина :attribute должна быть не больше :value символов.',
        'array' => 'Поле :attribute должно содержать не больше :value элементов.',
    ],
    'max' => [
        'numeric' => 'Поле :attribute не должно быть больше :max.',
        'file' => 'Размер файла :attribute не должен превышать :max кб.',
        'string' => 'Длина :attribute не должна превышать :max символов.',
        'array' => 'Поле :attribute не должно содержать более :max элементов.',
    ],
    'mimes' => 'Поле :attribute должно быть файлом одного из типов: :values.',
    'mimetypes' => 'Поле :attribute должно быть файлом одного из типов: :values.',
    'min' => [
        'numeric' => 'Поле :attribute должно быть не меньше :min.',
        'file' => 'Размер файла :attribute должен быть не меньше :min кб.',
        'string' => 'Длина :attribute должна быть не меньше :min символов.',
        'array' => 'Поле :attribute должно содержать не менее :min элементов.',
    ],
    'not_in' => 'Выбранное значение для :attribute недействительно.',
    'not_regex' => 'Поле :attribute имеет неверный формат.',
    'numeric' => 'Поле :attribute должно быть числом.',
    'password' => 'Неверный пароль.',
    'present' => 'Поле :attribute должно присутствовать.',
    'regex' => 'Поле :attribute имеет неверный формат.',
    'required' => 'Поле «:attribute» обязательно для заполнения.',
    'required_if' => 'Поле :attribute обязательно, когда :other равно :value.',
    'required_unless' => 'Поле :attribute обязательно, если :other не равно :values.',
    'required_with' => 'Поле :attribute обязательно, когда :values указано.',
    'required_with_all' => 'Поле :attribute обязательно, когда :values указаны.',
    'required_without' => 'Поле :attribute обязательно, когда :values не указано.',
    'required_without_all' => 'Поле :attribute обязательно, когда ни одно из :values не указано.',
    'same' => 'Поля :attribute и :other должны совпадать.',
    'size' => [
        'numeric' => 'Поле :attribute должно быть равно :size.',
        'file' => 'Размер файла :attribute должен быть :size кб.',
        'string' => 'Длина :attribute должна быть :size символов.',
        'array' => 'Поле :attribute должно содержать :size элементов.',
    ],
    'starts_with' => 'Поле :attribute должно начинаться с одного из следующих значений: :values.',
    'string' => 'Поле :attribute должно быть строкой.',
    'timezone' => 'Поле :attribute должно быть допустимым часовым поясом.',
    'unique' => 'Такое значение поля :attribute уже существует.',
    'uploaded' => 'Загрузка :attribute не удалась.',
    'url' => 'Поле :attribute должно быть допустимым URL-адресом.',
    'uuid' => 'Поле :attribute должно быть допустимым UUID.',

    /*
    |--------------------------------------------------------------------------
    | Человекопонятные названия полей
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        // Форма клиента (Clients\Index)
        'form.name' => 'имя',
        'form.phone' => 'телефон',
        'form.email' => 'email',
        'form.city' => 'город',
        'form.preferred_staff_id' => 'мастер',
        'form.client_id' => 'клиент',
        'form.staff_id' => 'мастер',
        'form.service_id' => 'услуга',
        'form.time' => 'время',
        'form.status' => 'статус',
        'form.notes' => 'заметки',
        'form.label' => 'описание',
        'form.amount' => 'сумма',
        'form.type' => 'тип',
        'form.occurred_at' => 'дата',
        'form.vk' => 'ВКонтакте',
        'form.telegram' => 'Telegram',
        'form.instagram' => 'Instagram',
        'form.whatsapp' => 'WhatsApp',

        // Форма мастера (Settings\Index)
        'staffForm.name' => 'имя',
        'staffForm.role_title' => 'должность',
        'staffForm.specialization' => 'специализация',
        'staffForm.color' => 'цвет',
        'staffForm.is_active' => 'статус',

        // Форма услуги (Settings\Index)
        'serviceForm.name' => 'название',
        'serviceForm.duration_min' => 'длительность',
        'serviceForm.price' => 'цена',
        'serviceForm.category' => 'категория',
        'serviceForm.is_active' => 'статус',

        // Форма пользователя (Settings\Index)
        'userForm.name' => 'имя',
        'userForm.email' => 'email',
        'userForm.role' => 'роль',
        'userForm.password' => 'пароль',
        'userForm.is_active' => 'статус',

        // Профиль бизнеса (Settings\Index)
        'businessName' => 'название бизнеса',
        'currencySymbol' => 'символ валюты',

        // Аутентификация (Auth\Login)
        'email' => 'email',
        'password' => 'пароль',

        // Импорт (Clients\Import)
        'file' => 'файл',
    ],

];
