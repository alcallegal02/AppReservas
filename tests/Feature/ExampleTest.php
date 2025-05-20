<?php

it('returns a successful response', function () {
    $response = $this->get('/');

    // Verifica que redirige a /login
    $response->assertRedirect('/login');
});