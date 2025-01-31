<?php
require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$bucketName = $_ENV['AWS_BUCKET_NAME'];
$region = $_ENV['AWS_REGION'];
$accessKey = $_ENV['AWS_ACCESS_KEY_ID'];
$secretKey = $_ENV['AWS_SECRET_ACCESS_KEY'];

$s3 = new S3Client([
    'version' => 'latest',
    'region'  => $region,
    'credentials' => [
        'key'    => $accessKey,
        'secret' => $secretKey,
    ],
]);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $file = $_FILES['image'];

    $fileName = 'Myphotos/' . time() . '_' . basename($file['name']);


    try {
        $result = $s3->putObject([
            'Bucket' => $bucketName,
            'Key'    => $fileName,
            'SourceFile' => $file['tmp_name'],
            // 'ACL'    => 'public-read',
            'ContentType' => $file['type']
        ]);

        echo "<p>✅ File uploaded successfully! <a href='{$result['ObjectURL']}' target='_blank'>View Image</a></p>";

    } catch (AwsException $e) {
        echo "<p>❌ Upload error: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Image to S3</title>
</head>
<body>
    <h2>Upload an Image</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="image" required>
        <button type="submit">Upload</button>
    </form>
</body>
</html>
