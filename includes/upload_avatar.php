<?php

// Include Database classs
require  'DB/Database.php';
require_once 'function.php';

Database::connect(require __DIR__ . '/DB/config.php');

// Include ImgPicker class
require 'imgPicker.php';

// Let's say that you grab the user id from the session
$userId = $_SESSION['membre_id'];

// ImgPicker options
$options = array(
    // Upload directory path
    'upload_dir' => __DIR__ . '/../membres/images/',
    // Upload directory url
    'upload_url' => '../membres/images/',
    // Image versions
    'versions' => array(
        // Generate 200x200 square image for the avatar
        'avatar' => array(
            'crop' => true,
            'max_width' => 200,
            'max_height' => 200
        )
    ),
    'load' => function () use ($userId) {
        // Select the image for the current user
        $db = new Database;
        $results = $db->table('images')
            ->where('id_membre', $userId)
            ->limit(1)
            ->get();

        if ($results) {
            return $results[0]->image;
        } else {
            return false;
        }
    },
    // Upload start callback
    'upload_start' => function ($image) use ($userId) {
        // Name the temp image as $userId
        $image->name = '~' . $userId . '.' . $image->type;
    },
    // Crop start callback
    'crop_start' => function ($image) use ($userId) {
        // Change the name of the image
        $image->name = $userId . '.' . $image->type;
    },
    // Crop complete callback
    'crop_complete' => function ($image) use ($userId) {
        // Save the image to database
        $data = array(
            'id_membre' => $userId,
            'image' =>  $userId . '-avatar.' . $image->type,
            'type' => 'avatar'
        );

        $db = new Database;
        // First check if the image exists
        $results = $db->table('images')
            ->where('id_membre', $userId)
            ->where('type', 'avatar')
            ->limit(1)
            ->get();

        // If exists update, otherwise insert
        if ($results) {
            $db->table('images')
                ->where('id_membre', $userId)
                ->where('type', 'avatar')
                ->limit(1)
                ->update($data);
        } else {
            $db->table('images')->insert($data);
            checkIfBadge($_SESSION['membre_id'], 0, 1);
        }
    }
);

// Create ImgPicker instance
new ImgPicker($options);
