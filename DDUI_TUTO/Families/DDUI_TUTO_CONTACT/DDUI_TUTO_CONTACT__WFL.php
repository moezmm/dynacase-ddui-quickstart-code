<?php

namespace DduiTuto;

class DDUI_TUTO_CONTACT__WFL extends \Dcp\Family\WDoc
{
    public $attrPrefix = 'ctc';
    public $firstState = self::e_creation;

    //region States
    const e_creation = 'ctc_e1';
    const e_up_to_date = 'ctc_e2';
    const e_to_update = 'ctc_e3';
    //endregion

    //region activities
    const a_creation = 'ctc_a1';
    const a_up_to_date = 'ctc_a2';
    const a_to_update = 'ctc_a3';
    //endregion

    //region Transitions
    const t_creation__up_to_date = 'ctc_t_e1__e2';
    const t_up_to_date__to_update = 'ctc_t_e2__e3';
    const t_to_update__up_to_date = 'ctc_t_e3__e2';
    //endregion

    public $stateactivity = [
        self::e_creation => self::a_creation,
        //self::e_E2 => self::e_A2,
        self::e_to_update => self::a_to_update
    ];

    public $transitions = [
        self::t_creation__up_to_date => [
            'nr' => true
        ],
        self::t_up_to_date__to_update => [
            'nr' => true
        ],
        self::t_to_update__up_to_date => [
            'nr' => true
        ]
    ];

    public $cycle = [
        [
            't' => self::t_creation__up_to_date,
            'e1' => self::e_creation,
            'e2' => self::e_up_to_date
        ],
        [
            't' => self::t_up_to_date__to_update,
            'e1' => self::e_up_to_date,
            'e2' => self::e_to_update
        ],
        [
            't' => self::t_to_update__up_to_date,
            'e1' => self::e_to_update,
            'e2' => self::e_up_to_date
        ]
    ];


}