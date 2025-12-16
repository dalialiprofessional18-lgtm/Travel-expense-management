<?php
// routes/routes.php

use App\Core\Middleware;

return [
    [ 'GET', '/', 'AuthController@landing' ],
    // === AUTHENTIFICATION ===
    [ 'GET',  '/login',          'AuthController@loginPage' ],
    [ 'POST', '/login',          'AuthController@login' ],
    [ 'GET',  '/register',       'AuthController@registerPage' ],
    [ 'POST', '/register',       'AuthController@register' ],
    [ 'GET',  '/logout',         'AuthController@logout' ],

    // === ADMIN ===
    [ 'GET',  '/admin',          'UserController@dashboard',        [ Middleware::class . '::requireRole', 'admin' ] ],
    [ 'GET',  '/users',          'UserController@index',             [ Middleware::class . '::requireRole', 'admin' ] ],
    [ 'GET',  '/users/create',   'UserController@createPage',        [ Middleware::class . '::requireRole', 'admin' ] ],
    [ 'POST', '/users/store',    'UserController@store',             [ Middleware::class . '::requireRole', 'admin' ] ],
    [ 'POST', '/users/{id}/role', 'UserController@updateRole',       [ Middleware::class . '::requireRole', 'admin' ] ],
    [ 'GET',  '/users/{id}/delete', 'UserController@delete',         [ Middleware::class . '::requireRole', 'admin' ] ],
// Dans routes/routes.php - Ajouter ces routes

// Actions Admin sur les notes de frais
// Dans /app/routes/routes.php
[ 'GET', '/assistant/context-debug', 'AssistantController@debugContext', [ Middleware::class . '::requireRole', ['admin', 'manager'] ] ],
['GET', '/demandes', 'UserController@demandes',             [Middleware::class . '::requireAuth']],

[
  'GET', 
  '/deplacements/{id}/map', 
  'DeplacementController@showMap'
],
['GET', '/users/{id}/edit', 'UserController@edit', [Middleware::class . '::requireRole', 'admin']],
['POST', '/users/{id}/update', 'UserController@updateUser'], [Middleware::class . '::requireRole', 'admin'],

['GET', '/calendrier', 'CalendrierController@index', [Middleware::class . '::requireAuth']],
['GET', '/api/calendrier/deplacements', 'CalendrierController@getDeplacementsJSON', [Middleware::class . '::requireAuth']],
['GET',  '/admin/note/{noteId}/view',     'UserController@viewNote', [Middleware::class . '::requireAuth']],
// === CALENDRIER ===
['GET',  '/calendrier',                    'CalendrierController@index',             [Middleware::class . '::requireAuth']],
['GET',  '/api/calendrier/deplacements',   'CalendrierController@getDeplacementsJSON', [Middleware::class . '::requireAuth']],
['GET',  '/api/calendrier/jour',           'CalendrierController@getDeplacementsJour', [Middleware::class . '::requireAuth']],
['GET',  '/calendrier/export-pdf',         'CalendrierController@exporterPDF',       [Middleware::class . '::requireAuth']],
  ['POST', '/admin/note/{id}/approve', 'UserController@approveNote', [Middleware::class . '::requireRole', 'admin']],
    
    // Rejeter une note (admin)
    ['POST', '/admin/note/{id}/reject', 'UserController@rejectNote', [Middleware::class . '::requireRole', 'admin']],
    
    // Révoquer une décision
    ['POST', '/admin/note/{id}/revoke', 'UserController@revokeDecision', [Middleware::class . '::requireRole', 'admin']],
    ['GET', '/ai', 'UserController@ai', [ Middleware::class . '::requireAuth' ] ],
    // === EMPLOYÉ ===
    [ 'GET',  '/employee',       'UserController@dashboard',     [ Middleware::class . '::requireAuth' ] ],
[ 'GET',  '/assistant',                    'AssistantController@index',           [ Middleware::class . '::requireRole', ['admin', 'manager'] ] ],
[ 'POST', '/assistant/chat',               'AssistantController@chat',            [ Middleware::class . '::requireRole', ['admin', 'manager'] ] ],
[ 'GET',  '/assistant/conversations',      'AssistantController@conversations',   [ Middleware::class . '::requireRole', ['admin', 'manager'] ] ],
[ 'GET',  '/assistant/conversation/{id}',  'AssistantController@loadConversation',[ Middleware::class . '::requireRole', ['admin', 'manager'] ] ],
[ 'GET',  '/assistant/delete/{id}',        'AssistantController@delete',          [ Middleware::class . '::requireRole', ['admin', 'manager'] ] ],
// Génération PDF
['GET', '/notes/{note_id}/pdf/download', 'NoteFraisController@downloadPDF'],
['GET', '/notes/{note_id}/pdf/preview',  'NoteFraisController@previewPDF'],
['GET',  '/verify-email',      'AuthController@verifyEmailPage'],
['POST', '/verify-email',      'AuthController@verifyEmail'],

['POST', '/resend-code',       'AuthController@resendCode'],
    [ 'GET',  '/deplacements/create', 'DeplacementController@createPage', [ Middleware::class . '::requireAuth' ] ],
    [ 'POST', '/deplacements/store',  'DeplacementController@store',      [ Middleware::class . '::requireAuth' ] ],
    [ 'GET',  '/deplacements/attribuer', 'DeplacementController@attribuerPage',   [ Middleware::class . '::requireAuth' ] ],
        [ 'POST', '/deplacements/attribuer', 'DeplacementController@attribuer',   [ Middleware::class . '::requireAuth' ] ],
    [ 'GET',  '/deplacements/{user_id}', 'DeplacementController@index'],
    [ 'GET',  '/deplacements/index', 'DeplacementController@indexPage',   [ Middleware::class . '::requireAuth' ] ],
    [ 'GET',  '/deplacements/delete/{id}', 'DeplacementController@delete',   [ Middleware::class . '::requireAuth' ] ],
// 1. عرض تفاصيل الطلب (الصفحة اللي عملناها بالتفصيل الفاخر)
['GET',  '/manager/deplacement/{deplacementId}',    'UserController@voirManager',    [ Middleware::class . '::requireRole' ] ],
// Routes Messagerie
['GET',  '/messagerie',                          'MessagerieController@index'],
['GET',  '/messagerie/conversation/{conversationId}',        'MessagerieController@conversation',       [Middleware::class . '::requireAuth']],
['POST', '/messagerie/send',                     'MessagerieController@sendMessage',        [Middleware::class . '::requireAuth']],
['POST', '/messagerie/start',                    'MessagerieController@startConversation',  [Middleware::class . '::requireAuth']],
['GET',  '/messagerie/delete/{conversationId}',              'MessagerieController@deleteConversation', [Middleware::class . '::requireAuth']],
// 2. تنفيذ الموافقة أو الرفض (من الزرين في الصفحة)
['POST', '/manager/action/{noteId}',     'UserController@actionManager',  [ Middleware::class . '::requireRole'] ],
    ['GET',  '/settings',           'UserController@settings',       [ Middleware::class . '::requireAuth' ] ],
    ['POST', '/settings/update',     'UserController@settingsUpdate',       [ Middleware::class . '::requireAuth' ]],
    // === NOTES DE FRAIS ===
    [ 'GET',  '/notes/{deplacement_id}',           'NoteFraisController@index',        fn( $p ) => Middleware::checkDeplacementAccess( $p[ 'deplacement_id' ] ) ],
    [ 'GET',  '/notes/{deplacement_id}/create',    'NoteFraisController@createPage',   fn( $p ) => Middleware::checkDeplacementOwner( $p[ 'deplacement_id' ] ) ],
    [ 'POST', '/notes/store',                      'NoteFraisController@store',        [ Middleware::class . '::requireAuth' ] ],
    [ 'POST', '/notes/updateStatut/{id}',                      'NoteFraisController@updateStatut',        [ Middleware::class . '::requireAuth' ] ],
    [ 'POST', '/notes/updateStatut1/{id}',                      'NoteFraisController@updateStatut1',        [ Middleware::class . '::requireAuth' ] ],
    [ 'POST', '/notes/updateStatut2/{id}',                      'NoteFraisController@updateStatut2',        [ Middleware::class . '::requireAuth' ] ],
['GET', '/profile', 'UserController@profile',        [ Middleware::class . '::requireAuth' ] ],
['POST', '/profile/update', 'UserController@profileUpdate',        [ Middleware::class . '::requireAuth' ] ],
['POST', '/profile/upload', 'UserController@profileUpload',        [ Middleware::class . '::requireAuth' ] ],
['POST', '/settings/profile',        'UserController@updateProfile',         [Middleware::class . '::requireAuth']],
['POST', '/settings/password',       'UserController@updatePassword',        [Middleware::class . '::requireAuth']],
['POST', '/settings/appearance',     'UserController@updateAppearance',      [Middleware::class . '::requireAuth']],
['POST', '/settings/notifications',  'UserController@updateNotifications',   [Middleware::class . '::requireAuth']],


// Page principale des déplacements de l'équipe
['GET', '/manager/deplacements-equipe', 'DeplacementController@deplacementsEquipe', [Middleware::class . '::requireAuth', Middleware::class . '::requireManager']],

// Actions sur les déplacements (GET pour liens directs)
['GET', '/manager/deplacement/{id}/approuver', 'DeplacementController@approuverDeplacement', [Middleware::class . '::requireAuth', Middleware::class . '::requireManager']],
['GET', '/manager/deplacement/{id}/rejeter', 'DeplacementController@rejeterDeplacement', [Middleware::class . '::requireAuth', Middleware::class . '::requireManager']],

// Actions sur les déplacements (POST pour formulaires)
['POST', '/manager/deplacement/{id}/approuver', 'DeplacementController@approuverDeplacement', [Middleware::class . '::requireAuth', Middleware::class . '::requireManager']],
['POST', '/manager/deplacement/{id}/rejeter', 'DeplacementController@rejeterDeplacement', [Middleware::class . '::requireAuth', Middleware::class . '::requireManager']],

// Voir les détails d'un déplacement
['GET', '/manager/deplacement/{id}', 'DeplacementController@voirDeplacement', [Middleware::class . '::requireAuth', Middleware::class . '::requireManager']],
// === SUPPORT & HELP ===
['GET', '/support', 'SupportController@index', [Middleware::class . '::requireAuth']],
['POST', '/support/ticket', 'SupportController@createTicket', [Middleware::class . '::requireAuth']],
['GET', '/support/tickets', 'SupportController@myTickets', [Middleware::class . '::requireAuth']],
['GET', '/support/ticket/{id}', 'SupportController@viewTicket', [Middleware::class . '::requireAuth']],
['POST', '/support/ticket/{id}/reply', 'SupportController@replyTicket', [Middleware::class . '::requireAuth']],

// Routes Admin pour gérer les tickets (optionnel)
['GET', '/admin/support', 'SupportController@adminIndex', [Middleware::class . '::requireRole', 'admin']],
['POST', '/admin/support/ticket/{id}/close', 'SupportController@closeTicket', [Middleware::class . '::requireRole', 'admin']],
// Upload images
['POST', '/settings/avatar',         'UserController@uploadAvatar',          [Middleware::class . '::requireAuth']],
['POST', '/settings/avatar/delete',  'UserController@deleteAvatar',          [Middleware::class . '::requireAuth']],
['POST', '/settings/cover',          'UserController@uploadCover',           [Middleware::class . '::requireAuth']],
['POST', '/settings/cover/delete',   'UserController@deleteCover',           [Middleware::class . '::requireAuth']],

    // === DÉTAILS FRAIS ===
    [ 'GET',  '/details/{note_id}',                'DetailsFraisController@index',     fn( $p ) => Middleware::checkNoteAccess( $p[ 'note_id' ] ) ],
    [ 'GET',  '/details/{note_id}/create',         'DetailsFraisController@createPage', fn( $p ) => Middleware::checkNoteOwner( $p[ 'note_id' ] ) ],
    [ 'POST', '/details/store',                    'DetailsFraisController@store',     [ Middleware::class . '::requireAuth' ] ],
    [ 'GET',  '/details/{id}/delete',    'DetailsFraisController@delete',    fn( $p ) => Middleware::checkNoteOwner( $p[ 'note_id' ] ) ],
    [ 'GET',  '/details/edit/{id}', 
    'DetailsFraisController@get', 
    fn($p) => Middleware::checkDetailOwner($p['id']) 
],    [ 'GET',  '/deplacements/edit/{id}', 
    'DeplacementController@get', 
],
['POST', '/deplacements/update/{id}', 'DeplacementController@edit'],
[ 'POST', '/details/update', 
    'DetailsFraisController@update', 
    [ Middleware::class . '::requireAuth' ] 
],

['GET',  '/admin/categories',              'CategoryController@index',      [Middleware::class . '::requireRole', 'admin']],
['GET',  '/admin/categories/create',       'CategoryController@createPage', [Middleware::class . '::requireRole', 'admin']],
['POST', '/admin/categories/store',        'CategoryController@store',      [Middleware::class . '::requireRole', 'admin']],
['GET',  '/admin/categories/edit/{id}',    'CategoryController@editPage',   [Middleware::class . '::requireRole', 'admin']],
['POST', '/admin/categories/update/{id}',  'CategoryController@update',     [Middleware::class . '::requireRole', 'admin']],
['GET',  '/admin/categories/delete/{id}',  'CategoryController@delete',     [Middleware::class . '::requireRole', 'admin']],


    [ 'GET', '/details/{id}/delete', 'DetailsFraisController@delete', fn($p) => Middleware::checkDetailOwner($p['id']) ],
    // === STATUT ===
    [ 'POST', '/deplacement/{id}/statut',          'DeplacementController@updateStatut', fn( $p ) => Middleware::checkDeplacementAccess( $p[ 'id' ] ) ],
    [ 'POST', '/note/{id}/statut',                 'NoteFraisController@updateStatut',   fn( $p ) => Middleware::checkNoteAccess( $p[ 'id' ] ) ],

    // === HISTORIQUE ===
    [ 'GET',  '/historique/deplacement/{deplacement_id}', 'HistoriqueStatutController@showDeplacement', fn( $p ) => Middleware::checkDeplacementAccess( $p[ 'deplacement_id' ] ) ],
    [ 'GET',  '/historique/note/{note_id}',        'HistoriqueStatutController@showNote',        fn( $p ) => Middleware::checkNoteAccess( $p[ 'note_id' ] ) ],

    // === MANAGER ===
    [ 'GET',  '/manager',        'UserController@dashboard',      [ Middleware::class . '::requireRole', [ 'manager', 'admin' ] ] ],
];