<?php

namespace DduiTuto;

use \Dcp\AttributeIdentifiers\DDUI_TUTO_CONTACT as DDUI_TUTO_CONTACT_Attributes;

Class DDUI_TUTO_CONTACT extends \Dcp\Family\Document {

    public function validateEmail($mail) {
        if('' !== $mail && false === filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            return "$mail is not a valid mail.";
        }
        return "";
    }

    /**
     * @apiExpose
     * associate with CVDoc DDUI_TUTO_CONTACT__CVDOC
     *
     * @return string
     */
    public function updateCR()
    {
        $cvDocId = getIdFromName(
            '', 'DDUI_TUTO_CONTACT__CVDOC', \Dcp\Family\Cvdoc::familyName
        );
        if ($cvDocId <= 0) {
            error_log (__METHOD__ . 'DDUI_TUTO_CONTACT__CVDOC does not exists');
            return '';
        }
        if ($this->cvid === $cvDocId) {
            error_log (__METHOD__ . 'DDUI_TUTO_CONTACT__CVDOC already associated with this document');
            return '';
        }
        $this->setCvid($cvDocId);
        $err = $this->modify(true, ['cvid'], true);
        if ($err) {
            error_log (__METHOD__ . "an error occured during association with DDUI_TUTO_CONTACT__CVDOC:\n$err");
            return '';
        }
        if ($this->cvid === $cvDocId) {
            error_log (__METHOD__ . 'DDUI_TUTO_CONTACT__CVDOC successfully associated with this document');
            return '';
        }
        error_log (__METHOD__ . 'an unknown error occured during association with DDUI_TUTO_CONTACT__CVDOC');
        return '';
    }


    public function preRefresh() {
        $this->updateCR();
    }
}
