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

    'accepted'             => 'Vous devez confirmer :attribute.',
    'active_url'           => 'Le champ \':attribute\' n\'est pas une URL valide.',
    'after'                => 'La date de :attribute doit être postérieure à :date.',
    'alpha'                => 'Le champ \':attribute\' doit se composer seulement de letres.',
    'alpha_dash'           => 'Le champ \':attribute\' doit se composer seulement de letres, chiffres et tiraits.',
    'alpha_num'            => 'Le champ \':attribute\' doit se composer seulement de letres et chiffres.',
    'array'                => 'Le champ \':attribute\' doit être un tableau.',
    'before'               => 'La date de :attribute doit être antérieure à :date.',
    'between'              => [
        'numeric' => 'La valeur du champ \':attribute\' doit être entre :min et :max.',
        'file'    => 'Le fichier indiqué dans le champ \':attribute\' doit être entre :min et :max kilobytes.',
        'string'  => 'Le champ \':attribute\' doit présenter entre :min et :max caractèrs.',
        'array'   => 'Le champ \':attribute\' doit comporter entre :min et :max éléments.',
    ],
    'boolean'              => 'Le champ \':attribute\' doit être vrai ou faux.',
    'confirmed'            => 'La confirmation du champ \':attribute\' ne correspond pas.',
    'date'                 => 'Le champ \':attribute\' doit être une date valide.',
    'date_format'          => 'Le champ \':attribute\' doit être formaté comme suit : :format.',
    'different'            => 'Les champ \':attribute\' et \':other\' doivent être différents.',
    'digits'               => 'Le champ \':attribute\' doit présenter :digits chiffres.',
    'digits_between'       => 'Le champ \':attribute\' doit présenter entre :min et :max chiffres.',
    'email'                => 'Le champ \':attribute\' doit être une adresse email valide.',
    'exists'               => 'Le champ \':attribute\' n\'existe pas.',
    'filled'               => 'The :attribute field is required.',
    'image'                => 'Le champ \':attribute\' doit être une image.',
    'in'                   => 'Le champ \':attribute\' n\'est pas valide.',
    'integer'              => 'Le champ \':attribute\' doit être un nombre entier.',
    'ip'                   => 'Le champ \':attribute\' doit être une adresse IP valide.',
    'json'                 => 'Le champ \':attribute\' doit être une chaîne de caractères JSON valide.',
    'max'                  => [
        'numeric' => 'La valeur champ \':attribute\' ne peut pas être plus grande que :max.',
        'file'    => 'Le fichier du champ \':attribute\' ne peut pas être plus lourd que :max kilobytes.',
        'string'  => 'Le champ \':attribute\' ne peut pas présenter plus de :max caractères.',
        'array'   => 'Le champ \':attribute\' ne peut pas présenter plus de :max éléments.',
    ],
    'mimes'                => 'Le champ \':attribute\' doit être un fichier de type: :values.',
    'min'                  => [
        'numeric' => 'La valeur du champ \':attribute\' doit être plus grande que :min.',
        'file'    => 'Le fichier du champ \':attribute\' doit être d\'au minimum :min kilobytes.',
        'string'  => 'Le champ \':attribute\' doit être long d\'au minimum :min caractères.',
        'array'   => 'Le champ \':attribute\' doit présenter au minimum :min éléments.',
    ],
    'not_in'               => 'Le champ \':attribute\' n\'est pas valide.',
    'numeric'              => 'Le champ \':attribute\' doit être un nombre.',
    'regex'                => 'Le format du champ \':attribute\' n\'est pas valide.',
    'required'             => 'Le champ \':attribute\' est obligatoire.',
    'required_if'          => 'Le champ \':attribute\' est obligatoire quand :other vaut :value.',
    'required_unless'      => 'Le champ \':attribute\' est obligatoire quand  :other ne vaut pas :values.',
    'required_with'        => 'Le champ \':attribute\' est obligatoire quand un ou plus des autres champs :values sont également remplis.',
    'required_with_all'    => 'Le champ \':attribute\' est obligatoire quand tous les autres champs :values sont remplis.',
    'required_without'     => 'Le champ \':attribute\' est obligatoire quand un ou plus des autres champs :values ne sont pas remplis.',
    'required_without_all' => 'Le champ \':attribute\' est obligatoire quand aucun des autres champs :values ne sont pas remplis.',
    'same'                 => 'Les champ \':attribute\' et \':other\' doivent être identiques.',
    'size'                 => [
        'numeric' => 'Le champ \':attribute\' doit être égal à :size.',
        'file'    => 'Le fichier du champ \':attribute\' doit peser :size kilobytes.',
        'string'  => 'Le champ \':attribute\' doit être long d\'exactement :size caractères.',
        'array'   => 'Le champ \':attribute\' doit présenter :size éléments.',
    ],
    'string'               => 'Le champ \':attribute\' doit être une chaîne de caractères.',
    'timezone'             => 'Le champ \':attribute\' doit être un fuseau horaire valide.',
    'unique'               => 'Ce :attribute est déjà utilisé.',
    'url'                  => 'Le champ \':attribute\' doit être une URL valide.',

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
        'name' => [
            'unique' => 'Ce nom est déjà utilisé',
        ],
        'email' => [
            'unique' => 'Cet email est déjà utilisé',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        'name' => 'nom',
        'email' => 'email',
        'password' => 'mot de passe',

    ],

];
