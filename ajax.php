<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure the 'uploads' directory exists
    if (!file_exists('uploads')) {
        mkdir('uploads', 0777, true);
    }

    // Handle file upload
    if (isset($_FILES['file'])) {
        $targetDir = 'uploads/';
        $targetFile = $targetDir . basename($_FILES['file']['name']);

        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
            echo "success";
        } else {
            echo "error";
        }
        die();
    }

    // Recursive function to list files and folders
    function listFilesAndFolders($dir)
    {
        $result = [];
        $items = scandir($dir);

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                $result[] = [
                    'name' => $item,
                    'type' => 'folder',
                    'path' => $path,
                    'contents' => listFilesAndFolders($path), // Recursively list folder contents
                ];
            } else {
                $result[] = [
                    'name' => $item,
                    'type' => 'file',
                    'path' => $path,
                ];
            }
        }

        return $result;
    }

    // Handle file and folder listing for both the current directory and 'uploads' directory
    if (isset($_POST['action']) && $_POST['action'] === 'list') {
        // List files and folders from both the current directory and 'uploads' directory
        $files = array_merge(listFilesAndFolders(__DIR__), listFilesAndFolders('uploads'));
        echo json_encode($files); // Return the list as JSON
        die();
    }
}
?>
