<?php

require_once('qrlib.php');

class PromptPayQR {
    public $size = 4;
    public $id = '';
    public $amount = 0;

    public function generate($file = false) {
        // Format [Fee][Length][Data]
        $data = '000201'; // Start
        $data .= '010211'; // Accept recycle

        // Merchant account information
        $merchantInfo = '0016A000000677010111'; // Application ID

        // PromptPay ID
        $merchantInfo .= '01';
        if (strlen($this->id) == 13) { // ID card
            $merchantInfo .= '1500' . $this->id;
        } else if (strlen($this->id) == 10) {
            $merchantInfo .= '130066' . substr($this->id, -9);
        } else {
            return false;
        }

        $data .= '29' . strlen($merchantInfo) . $merchantInfo; // Set merchant account information
        $data .= '5802TH'; // Thai baht

        // Amount
        if ($this->amount > 0) {
            $amount = floatval($this->amount); // Convert to float
            $amountText = number_format($amount, 2, '.', ''); // Format amount
            $amountLen = strlen($amountText);
            $data .= '54' . ($amountLen < 10 ? '0' . $amountLen : $amountLen) . $amountText;
        }

        $data .= '5303764'; // 764 is Thai baht in ISO4217

        // Checksum
        $data .= '6304';
        $sum = strtoupper(dechex($this->crc16($data)));
        $data .= $sum;

        $file = $file === false ? 'TMP_FILE_QRCODE_PROMPTPAY.png' : $file;

        QRcode::png($data, $file, QR_ECLEVEL_H, $this->size, 2);
        // if ($file == 'TMP_FILE_QRCODE_PROMPTPAY.png') {
            return file_exists($file) ? 'data:image/png;base64,' . base64_encode(file_get_contents($file)) : false;
        // }

        // return $data;
    }

    /*
     * CRC16 function from https://stackoverflow.com/questions/14018508/how-to-calculate-crc16-in-php
     * RomKazanova, thank you for the code.
     */
    private function crc16($data) {
        $crc = 0xFFFF;
        for ($i = 0; $i < strlen($data); $i++) {
            $x = (($crc >> 8) ^ ord($data[$i])) & 0xFF;
            $x ^= $x >> 4;
            $crc = (($crc << 8) ^ ($x << 12) ^ ($x << 5) ^ $x) & 0xFFFF;
        }
        return $crc;
    }
}
