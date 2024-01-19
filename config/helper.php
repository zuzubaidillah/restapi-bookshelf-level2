<?php

function auth() {
    return AuthMiddleware::globalAuth();
}