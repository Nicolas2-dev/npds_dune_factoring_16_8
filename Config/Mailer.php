<?php

/**
 * Mailer configuration.
 */
return array(

    /**
     * Site Administrator's Email
     */
    'adminmail' => 'webmaster@site.fr',

    /**
     * What Mail function to be used (1=mail, 2=email)
     */
    'mail_fonction' => 1,

    /**
     * Notify you each time your site receives a news submission? (1=Yes 0=No)
     */
    'notify' => 1,

    /**
     * Email, address to send the notification
     */
    'notify_email' => 'webmaster@site.fr',

    /**
     * Email subject
     */
    'notify_subject' => 'Nouvelle soumission',

    /**
     * Email body, message
     */
    'notify_message' => 'Le site a recu une nouvelle soumission !',

    /**
     * account name to appear in From field of the Email
     */
    'notify_from' => 'webmaster@site.fr',

    // Php Mailer configuration

    /**
     * Configurer le serveur SMTP
     */
    'smtp_host' => '',

    /**
     * Port TCP, utilisez 587 si vous avez activé le chiffrement TLS
     */
    'smtp_port' => '',

    /**
     * Activer l'authentification SMTP
     */
    'smtp_auth' => 0,

    /**
     * Nom d'utilisateur SMTP
     */
    'smtp_username' => '',

    /**
     * Mot de passe SMTP
     */
    'smtp_password' => '',

    /**
     * Activer le chiffrement TLS
     */
    'smtp_secure' => 0,

    /**
     * Type du chiffrement TLS
     */
    'smtp_crypt' => 'tls',

    /**
     * DKIM 1 pour celui du dns 2 pour une génération automatique
     */
    'dkim_auto' => 1,
);
