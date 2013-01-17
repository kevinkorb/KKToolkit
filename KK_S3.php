<?php
class KK_S3 {
	
	public static function putFile($local, $target, $bucket = null) {
		if(!$bucket) {
			$bucket = S3_BUCKET;
		}
		$s3 = new S3(S3_ACCESS_KEY_ID, S3_SECRET_ACCESS_KEY);
		return $s3->putObjectFile($local, $bucket, $target, S3::ACL_PUBLIC_READ);
	}
	
	public static function putBackupFile($local, $target, $bucket = null) {
		if(!$bucket) {
			$bucket = S3_BUCKET_BACKUP;
		}
		$s3 = new S3(S3_ACCESS_KEY_ID, S3_SECRET_ACCESS_KEY);
		echo "Storing file {$local} to s3\n";
		return $s3->putObjectFile($local, $bucket, $target, S3::ACL_PRIVATE);
	}
	
	public static function getBucketFileList($bucket = null) {
		if(!$bucket) {
			$bucket = S3_BUCKET_BACKUP;
		}
		$s3 = new S3(S3_ACCESS_KEY_ID, S3_SECRET_ACCESS_KEY);
		return $s3->getBucket($bucket);
	}
	
	public static function deleteFile($file, $bucket = null) {
		if(!$bucket) {
			$bucket = S3_BUCKET_BACKUP;
		}
		$s3 = new S3(S3_ACCESS_KEY_ID, S3_SECRET_ACCESS_KEY);
		$s3->deleteObject($bucket, $file);
	}
	
	public static function PutFileToS3ReturnPath($sourceFile, $targetLocation){
		for($c=0; $c<5; $c++){
			if(self::putFile($sourceFile, $targetLocation, S3_BUCKET)){
				return "http://s3.amazonaws.com/" . S3_BUCKET . "/" . $targetLocation;
			}
			else {
				throw new Exception("Put file failed");
			}
		}
		return false;
	}
	
	public static function downloadAndPutFileToS3($sourceFile, $targetLocation){
		for($c=0; $c<9; $c++){
			$tmpFile = "/tmp/" . microtime(true) . rand(111, 444);
			$contents = KK_HTTP::get_contents($sourceFile);
			try{
				if(strlen($contents) > 150){
					if(substr(strtolower(trim($contents)), 0, 6) != '<?xml '){
						if(file_put_contents($tmpFile, $contents)) {
							if(self::putFile($tmpFile, $targetLocation, S3_BUCKET)){
								return "http://s3.amazonaws.com/" . S3_BUCKET . "/" . $targetLocation;
							}
							else {
								throw new Exception("Put file failed");
							}
						}
						else {
							throw new Exception("Unable to write contents to $tmpFile");
						}
					}
					else {
						throw new Exception("File is XML, assume it is an error");
					}
				}
				else {
					throw new Exception("Contents was less than 150 bytes");
				}
			}
			catch (exception $e){
				error_log($e->getMessage());
				sleep(1);
			}
		}
		return false;
	}
	
}