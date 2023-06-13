<?php

class PasswordGeneratorTest extends CTestCase {

	public function testSha1() {
		$string = "123456";
		$this->assertEquals("7c4a8d09ca3762af61e59520943dc26494f8941b", PasswordGenerator::sha1($string));
		$string = "admin";
		$this->assertEquals("d033e22ae348aeb5660fc2140aec35850c4da997", PasswordGenerator::sha1($string));
		$string = "123";
		$this->assertEquals("40bd001563085fc35165329ea1ff5c5ecbdbbeef", PasswordGenerator::sha1($string));
		$string = "batatinha";
		$this->assertEquals("b3c6110deda7bf1d7275c31780a7a14716302195", PasswordGenerator::sha1($string));
		$string = "1__+d.;2<4$#9><4>0!@.$";
		$this->assertEquals("cab17855497ae7b7033bce1f1d85770a6cb57201", PasswordGenerator::sha1($string));
	}

	/*
	  public function testVerificarSsha()
	  {
	  $string = "123456";
	  $original = "{SSHA}VqB6vMlT/4WNnGl9ecKE8b5Eb1IqmXps";
	  $this->assertEquals($original, PasswordGenerator::ssha($string, $original));
	  $string = "admin";
	  $original = "{SSHA}AHaS7vl5tooLWLwUphoSMASH37QaniF7";
	  $this->assertEquals($original, PasswordGenerator::ssha($string, $original));
	  $string = "123";
	  $original = "{SSHA}LZXq31fT6ubyQyToSxNT+9xy/PGJ/yX3";
	  $this->assertEquals($original, PasswordGenerator::ssha($string, $original));
	  $string = "batatinha";
	  $original = "{SSHA}tl7R9dSWeSi2ayKH6ozP5cATiAOYe+dX";
	  $this->assertEquals($original, PasswordGenerator::ssha($string, $original));
	  $string = "1__+d.;2<4$#9><4>0!@.$";
	  $original = "{SSHA}5sx0guSt9ZjajojsAyFZ70yh15S36Or5";
	  $this->assertEquals($original, PasswordGenerator::ssha($string, $original));

	  $string = "nova senha cujo hash será gerado com sal aleatório";
	  $sal = "Sal Não É Ajinomoto-_124561891!@#!$!@%";
	  $hash = '{SSHA}' . base64_encode(sha1($string . $sal, true) . $sal);
	  $this->assertEquals($hash, PasswordGenerator::ssha($string, $hash));
	  }

	  public function testMd5()
	  {
	  $string = "123456";
	  $this->assertEquals("{MD5}4QrcOUm6Wau+VuBX8g+IPg==", PasswordGenerator::md5($string));
	  $string = "admin";
	  $this->assertEquals("{MD5}ISMvKXpXpadDiUoOSoAfww==", PasswordGenerator::md5($string));
	  $string = "123";
	  $this->assertEquals("{MD5}ICy5YqxZB1uWSwcVLSNLcA==", PasswordGenerator::md5($string));
	  $string = "batatinha";
	  $this->assertEquals("{MD5}06o0nI2TLqcfEaoJa6KfYQ==", PasswordGenerator::md5($string));
	  $string = "1__+d.;2<4$#9><4>0!@.$";
	  $this->assertEquals("{MD5}McyrriUojICw4B8W8w4rpA==", PasswordGenerator::md5($string));
	  }
	 */
}
