<?php

$mockMailer = $this->createMock(Mailer::class);
$mockMailer->expects($this->once())
           ->method('send')
           ->with('test@example.com');

$service = new UserService($mockMailer);
$service->register(new User('test@example.com'));