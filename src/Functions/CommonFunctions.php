<?php

namespace BShare\Webservice\Functions;

/**
 * Common functions to be used
 */
class CommonFunctions
{
    /**
     * Insert file into server, and rename the file
     * @param array $file file array
     * @param array $title request file title
     * @return array|string filename
     */
    public static function insertFileIntoServer(array &$file, string $title, string $paste, bool $multiImages = false)
    {
        $date = date('Y-m-d');

        $tmpFile = $file['tmp_name'];
        $filename = $file['name'];

        if ($multiImages) {
            $data = array_map(function ($filename, $tmpFilename) {
                return [
                    "filename" => $filename,
                    "tmpFilename" => $tmpFilename
                ];
            }, $file['name'], $file['tmp_name']);

            foreach ($data as &$value) {
                $filename = "$title-$date." . pathinfo($value['filename'], PATHINFO_EXTENSION);
                $uploadFile = $_SERVER['DOCUMENT_ROOT'] . "/assets/$paste/" . $filename;

                if (!move_uploaded_file($value['tmpFilename'], $uploadFile)) {
                    // Validate the move file
                    die('Não conseguimos mover o arquivo');
                }

                // Rename the file in data
                $value = $filename;
            }

            // Rename file
            return $data;
        } else {
            $filename = "$title-$date." . pathinfo($filename, PATHINFO_EXTENSION);
            $uploadFile = $_SERVER['DOCUMENT_ROOT'] . "/assets/$paste/$filename";

            if (!@move_uploaded_file($tmpFile, $uploadFile)) {
                // Validate the move file
                die('Não conseguimos mover o arquivo');
            }

            // Rename the file
            return $filename;
        }
    }

    /**
     * Generate UUID version 4
     * @return string UUID
     */
    public static function UUIDV4()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

            // 32 bits for "time_low"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),

            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
}
