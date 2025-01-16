<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => 'يجب قبول :attribute.',
    'active_url'           => ':attribute لا يُمثّل رابطًا صحيحًا.',
    'after'                => 'يجب على :attribute أن يكون تاريخًا لاحقًا للتاريخ :date.',
    'after_or_equal'       => ':attribute يجب أن يكون تاريخاً لاحقاً أو مطابقاً للتاريخ :date.',
    'alpha'                => 'يجب أن لا يحتوي :attribute سوى على حروف.',
    'alpha_dash'           => 'يجب أن لا يحتوي :attribute سوى على حروف، أرقام ومطّات.',
    'alpha_num'            => 'يجب أن يحتوي :attribute على حروفٍ وأرقامٍ فقط.',
    'array'                => 'يجب أن يكون :attribute ًمصفوفة.',
    'before'               => 'يجب على :attribute أن يكون تاريخًا سابقًا للتاريخ :date.',
    'before_or_equal'      => ':attribute يجب أن يكون تاريخا سابقا أو مطابقا للتاريخ :date.',
    'between'              => [
        'numeric' => 'يجب أن تكون قيمة :attribute بين :min و :max.',
        'file'    => 'يجب أن يكون حجم الملف :attribute بين :min و :max كيلوبايت.',
        'string'  => 'يجب أن يكون عدد حروف النّص :attribute بين :min و :max.',
        'array'   => 'يجب أن يحتوي :attribute على عدد من العناصر بين :min و :max.',
    ],
    'boolean'              => 'يجب أن تكون قيمة :attribute إما true أو false .',
    'confirmed'            => 'حقل التأكيد غير مُطابق للحقل :attribute.',
    'date'                 => ':attribute ليس تاريخًا صحيحًا.',
    'date_equals'          => 'يجب أن يكون :attribute مطابقاً للتاريخ :date.',
    'date_format'          => 'لا يتوافق :attribute مع الشكل :format.',
    'different'            => 'يجب أن يكون الحقلان :attribute و :other مُختلفين.',
    'digits'               => 'يجب أن يحتوي :attribute على :digits رقمًا/أرقام.',
    'digits_between'       => 'يجب أن يحتوي :attribute بين :min و :max رقمًا/أرقام .',
    'dimensions'           => 'الـ :attribute يحتوي على أبعاد صورة غير صالحة.',
    'distinct'             => 'للحقل :attribute قيمة مُكرّرة.',
    'email'                => 'يجب أن يكون :attribute عنوان بريد إلكتروني صحيح البُنية.',
    'ends_with'            => 'يجب أن ينتهي :attribute بأحد القيم التالية: :values',
    'exists'               => 'القيمة المحددة :attribute غير موجودة.',
    'file'                 => 'الـ :attribute يجب أن يكون ملفا.',
    'filled'               => ':attribute إجباري.',
    'gt'                   => [
        'numeric' => 'يجب أن تكون قيمة :attribute أكبر من :value.',
        'file'    => 'يجب أن يكون حجم الملف :attribute أكبر من :value كيلوبايت.',
        'string'  => 'يجب أن يكون طول النّص :attribute أكثر من :value حروفٍ/حرفًا.',
        'array'   => 'يجب أن يحتوي :attribute على أكثر من :value عناصر/عنصر.',
    ],
    'gte'                  => [
        'numeric' => 'يجب أن تكون قيمة :attribute مساوية أو أكبر من :value.',
        'file'    => 'يجب أن يكون حجم الملف :attribute على الأقل :value كيلوبايت.',
        'string'  => 'يجب أن يكون طول النص :attribute على الأقل :value حروفٍ/حرفًا.',
        'array'   => 'يجب أن يحتوي :attribute على الأقل على :value عُنصرًا/عناصر.',
    ],
    'image'                => 'يجب أن يكون :attribute صورةً.',
    'in'                   => ':attribute غير موجود.',
    'in_array'             => ':attribute غير موجود في :other.',
    'integer'              => 'يجب أن يكون :attribute عددًا صحيحًا.',
    'ip'                   => 'يجب أن يكون :attribute عنوان IP صحيحًا.',
    'ipv4'                 => 'يجب أن يكون :attribute عنوان IPv4 صحيحًا.',
    'ipv6'                 => 'يجب أن يكون :attribute عنوان IPv6 صحيحًا.',
    'json'                 => 'يجب أن يكون :attribute نصًا من نوع JSON.',
    'lt'                   => [
        'numeric' => 'يجب أن تكون قيمة :attribute أصغر من :value.',
        'file'    => 'يجب أن يكون حجم الملف :attribute أصغر من :value كيلوبايت.',
        'string'  => 'يجب أن يكون طول النّص :attribute أقل من :value حروفٍ/حرفًا.',
        'array'   => 'يجب أن يحتوي :attribute على أقل من :value عناصر/عنصر.',
    ],
    'lte'                  => [
        'numeric' => 'يجب أن تكون قيمة :attribute مساوية أو أصغر من :value.',
        'file'    => 'يجب أن لا يتجاوز حجم الملف :attribute :value كيلوبايت.',
        'string'  => 'يجب أن لا يتجاوز طول النّص :attribute :value حروفٍ/حرفًا.',
        'array'   => 'يجب أن لا يحتوي :attribute على أكثر من :value عناصر/عنصر.',
    ],
    'max'                  => [
        'numeric' => 'يجب أن تكون قيمة :attribute مساوية أو أصغر من :max.',
        'file'    => 'يجب أن لا يتجاوز حجم الملف :attribute :max كيلوبايت.',
        'string'  => 'يجب أن لا يتجاوز طول النّص :attribute :max حروفٍ/حرفًا.',
        'array'   => 'يجب أن لا يحتوي :attribute على أكثر من :max عناصر/عنصر.',
    ],
    'mimes'                => 'يجب أن يكون ملفًا من نوع : :values.',
    'mimetypes'            => 'يجب أن يكون ملفًا من نوع : :values.',
    'min'                  => [
        'numeric' => 'يجب أن تكون قيمة :attribute مساوية أو أكبر من :min.',
        'file'    => 'يجب أن يكون حجم الملف :attribute على الأقل :min كيلوبايت.',
        'string'  => 'يجب أن يكون طول النص :attribute على الأقل :min حروفٍ/حرفًا.',
        'array'   => 'يجب أن يحتوي :attribute على الأقل على :min عُنصرًا/عناصر.',
    ],
    'not_in'               => 'العنصر :attribute غير صحيح.',
    'not_regex'            => 'صيغة :attribute غير صحيحة.',
    'numeric'              => 'يجب على :attribute أن يكون رقمًا.',
    'password'             => 'كلمة المرور غير صحيحة.',
    'present'              => 'يجب تقديم :attribute.',
    'regex'                => 'صيغة :attribute .غير صحيحة.',
    'required'             => ':attribute مطلوب.',
    'required_if'          => ':attribute مطلوب في حال ما إذا كان :other يساوي :value.',
    'required_unless'      => ':attribute مطلوب في حال ما لم يكن :other يساوي :values.',
    'required_with'        => ':attribute مطلوب إذا توفّر :values.',
    'required_with_all'    => ':attribute مطلوب إذا توفّر :values.',
    'required_without'     => ':attribute مطلوب إذا لم يتوفّر :values.',
    'required_without_all' => ':attribute مطلوب إذا لم يتوفّر :values.',
    'same'                 => 'يجب أن يتطابق :attribute مع :other.',
    'size'                 => [
        'numeric' => 'يجب أن تكون قيمة :attribute مساوية لـ :size.',
        'file'    => 'يجب أن يكون حجم الملف :attribute :size كيلوبايت.',
        'string'  => 'يجب أن يحتوي النص :attribute على :size حروفٍ/حرفًا بالضبط.',
        'array'   => 'يجب أن يحتوي :attribute على :size عنصرٍ/عناصر بالضبط.',
    ],
    'starts_with'          => 'يجب أن يبدأ :attribute بأحد القيم التالية: :values',
    'string'               => 'يجب أن يكون :attribute نصًا.',
    'timezone'             => 'يجب أن يكون :attribute نطاقًا زمنيًا صحيحًا.',
    'unique'               => 'قيمة :attribute مُستخدمة من قبل.',
    'uploaded'             => 'فشل في تحميل الـ :attribute.',
    'url'                  => 'صيغة الرابط :attribute غير صحيحة.',
    'uuid'                 => ':attribute يجب أن يكون بصيغة UUID سليمة.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'description' => 'الوصف',
        'phone' => 'رقم الهاتف',
        'address' => 'العنوان',

        'email' => 'البريد الالكتروني',
        'password' => 'كلمة المرور',
        'password_confirmation' => 'تأكيد كلمة المرور',
        'image' => 'الصورة',
        'permissions' => 'الصلاحيات',
        'address' => 'عنوان السكن',


        
        'name' => 'الإسم',
        'category_id' => 'فئة الصنف',
        'item_type' => 'نوع الصنف',
        'retal_uom_id' => 'وحدة قياس التجزئة',
        'uom_id' => 'وحدة القياس الاب',
        'uom_add_id' => 'الوحدة',
        'does_has_reta_unit' => 'وحدة تجزئه ابن',
        'retal_qt_to_parent'=>'عدد وحدات التجزئه',

        // اسعار الوحدة الاب
        'price' => 'سعر القطاعي للوحدة الاب',
        'nos_gomla_price' => 'سعر نص جمله للوحدة الاب',
        'gomla_price' => 'سعر جمله للوحدة الاب',
        'post_price' => 'سعر تكلفة الشراء للوحدة الاب',

        // اسعار الوحدة الابن
        'price_retal' => 'سعر القطاعي للوحدة التجزئه',
        'nos_gomla_price_retal' => 'سعر نص جمله للوحدة التجزئه',
        'gomla_price_retal' => 'سعر جمله للوحدة التجزئه',
        'cost_price_retal' => 'سعر تكلفة الشراء للوحدة التجزئه',
        'has_fixced_price' => 'هل للصنف سعر ثابت',
        'barcode' => 'الباركود',

        'balance_name' => 'إسم الحساب',
        'account_type' => 'نوع الحساب',
        'start_balance' => 'رصيد افتتاحي',
        'is_parent' => 'هل الحساب اب',
        'parent_account_num.required_if' => 'الحساب الاب',
        'start_balance_status' => 'حالة الحساب اول المدة',
        'parent_account_num' => 'الحساب الاب',
        'customer_name' => 'إسم العميل',
        'customer_parent_account_num' => 'رقم الحساب المالي للموردين الاب',
        'em_parent_account_num' => 'رقم الحساب المالي للموظفين الاب',
        'supplier_name' => 'اسم المورد',
        'supplier_category_id' => 'فئة المورد',
        'supplier_code' => 'اسم المورد',
        'pill_type' => 'نوع الفاتورة',
        'inv_item_card_id' => 'الصنف',
        'production_date' => 'تاريخ الانتاج',
        'expire_date' => 'تاريخ الانتهاء',
        'production_expire_date_error' => 'لا يمكن ان يكون تاريخ الانتهاء اقل من تاريخ الانتاج',
        
        'unit_price' => 'سعر الوحدة',
        'quantity' => 'الكمية',
        'store_id' => 'المخزن المستلم',
        'job_id' => 'نوع الوظيفة',
        'do_has_shift' => 'الشفت ',
        'shift_type_id' => 'نوع الشفت ',
        'departement_id' => 'الإدارة',
        'total_hours' => 'عدد الساعات',
        'salary' => 'المرتب',
        'does_has_social_insurance' => 'التأمين الإجتماعي',
        'social_insurance_value' => 'التأمين الإجتماعي',
        'social_insurance_num' => 'رقم التأمين الإجتماعي',
        'does_has_allowances' => 'بدلات ثابته',
        'allowances_value' => 'قيمة بدلات ثابته',
        'do_has_social_motivation' => 'هل له حافز شهري',
        'motivation_value' => 'قيمة الحافز الشهري',
        'social_insurance' => 'هل له تأمين إجتماعي',        
        'order_date' => 'تاريخ الفاتورة',

        'treasury_id' => 'الخزنة',
        'move_type_id' => 'نوع الحركة',
        'money' => 'قيمة المبلغ',
        'byan' => 'البيان',
        'account_id' => 'الحساب المالي',
        'date' => 'التاريخ',
        'account_num' =>'رقم الحساب',
        
    ],

];
