<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ExpenseFlow - Gestion des Frais de Déplacement</title>
    <link rel="stylesheet" href="./bootstrap-5.3.7-dist/css/bootstrap.min.css" />
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark position-fixed w-100 py-3" style="background: rgba(0,0,0,0.8); backdrop-filter: blur(10px); z-index: 1000;">
        <div class="container">
           <a
                class="navbar-brand d-flex align-items-center"
                href="./index.html"
              >
                <img
                  src="../../assets/Logo.png"
                  alt="Logo"
                  height="60"
                  width="60"
                  class="d-inline-block align-text-top me-2"
                />
                <h6 class="h6 mb-0 fw-bold text-gray">MOROCODEMOVE</h6>
              </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item me-3">
                        <a class="nav-link" href="#features">Fonctionnalités</a>
                    </li>
                    <li class="nav-item me-3">
                        <a class="nav-link" href="#workflow">Comment ça marche</a>
                    </li>
                    <li class="nav-item me-3">
                        <a class="nav-link" href="#benefits">Avantages</a>
                    </li>
                    <li class="nav-item me-3">
                        <a class="btn btn-outline-light rounded-pill px-4" href="/login">Connexion</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-light rounded-pill px-4 fw-semibold" href="/register">S'inscrire</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="position-relative overflow-hidden" style="padding-top: 100px; min-height: 100vh; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="position-absolute w-100 h-100 top-0 start-0" style="opacity: 0.1;">
            <div class="position-absolute rounded-circle bg-white" style="width: 400px; height: 400px; top: -100px; right: -100px;"></div>
            <div class="position-absolute rounded-circle bg-white" style="width: 300px; height: 300px; bottom: -50px; left: -50px;"></div>
        </div>
        
        <div class="container position-relative py-5">
            <div class="row align-items-center min-vh-100">
                <div class="col-lg-6 text-white mb-5 mb-lg-0">
                    <div class="badge bg-white bg-opacity-25 text-white px-3 py-2 rounded-pill mb-4">
                        <svg width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                            <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
                        </svg>
                        Solution Digitale Complète
                    </div>
                    <h1 class="display-3 fw-bold mb-4">Simplifiez la gestion de vos frais de déplacement</h1>
                    <p class="lead mb-4 opacity-90">Automatisez la soumission, la validation et le remboursement des frais professionnels. Gain de temps garanti.</p>
                    <div class="d-flex flex-wrap gap-3 mb-5">
                        <a href="/register" class="btn btn-light btn-lg rounded-pill px-5 py-3 fw-semibold shadow">
                            Commencer maintenant
                            <svg width="20" height="20" fill="currentColor" class="ms-2" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                            </svg>
                        </a>
                        <a href="#workflow" class="btn btn-outline-light btn-lg rounded-pill px-5 py-3 fw-semibold">
                            Découvrir plus
                        </a>
                    </div>
                    <div class="d-flex gap-4 mt-5">
                        <div>
                            <h3 class="display-6 fw-bold mb-0">98%</h3>
                            <p class="opacity-75 mb-0">Satisfaction</p>
                        </div>
                        <div class="border-start border-white border-opacity-25 ps-4">
                            <h3 class="display-6 fw-bold mb-0">-75%</h3>
                            <p class="opacity-75 mb-0">Temps de traitement</p>
                        </div>
                        <div class="border-start border-white border-opacity-25 ps-4">
                            <h3 class="display-6 fw-bold mb-0">100%</h3>
                            <p class="opacity-75 mb-0">Digital</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="position-relative">
                        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                            <div class="card-body p-0">
                                <div class="bg-dark text-white p-4">
                                    <div class="d-flex align-items-center gap-3 mb-3">
                                        <div class="bg-danger rounded-circle" style="width: 12px; height: 12px;"></div>
                                        <div class="bg-warning rounded-circle" style="width: 12px; height: 12px;"></div>
                                        <div class="bg-success rounded-circle" style="width: 12px; height: 12px;"></div>
                                    </div>
                                    <h5 class="mb-0">Tableau de bord</h5>
                                </div>
                                <div class="p-4 bg-white">
                                    <div class="row g-3 mb-4">
                                        <div class="col-6">
                                            <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                                <div class="text-primary mb-2">
                                                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                                        <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
                                                    </svg>
                                                </div>
                                                <h4 class="fw-bold mb-0">15</h4>
                                                <small class="text-muted">Validées</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                                                <div class="text-warning mb-2">
                                                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                                        <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
                                                    </svg>
                                                </div>
                                                <h4 class="fw-bold mb-0">8</h4>
                                                <small class="text-muted">En attente</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="border rounded-3 p-3 mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="fw-semibold">Mission Paris</span>
                                            <span class="badge bg-success rounded-pill">Validée</span>
                                        </div>
                                        <div class="d-flex justify-content-between text-muted small">
                                            <span>Transport + Hébergement</span>
                                            <span class="fw-bold text-dark">4500,00 MAD</span>
                                        </div>
                                    </div>
                                    <div class="border rounded-3 p-3 mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="fw-semibold">Formation Lyon</span>
                                            <span class="badge bg-warning rounded-pill">En cours</span>
                                        </div>
                                        <div class="d-flex justify-content-between text-muted small">
                                            <span>Repas + Parking</span>
                                            <span class="fw-bold text-dark">850,50 MAD</span>
                                        </div>
                                    </div>
                                    <div class="border rounded-3 p-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="fw-semibold">Visite client Marseille</span>
                                            <span class="badge bg-primary rounded-pill">Nouvelle</span>
                                        </div>
                                        <div class="d-flex justify-content-between text-muted small">
                                            <span>Carburant</span>
                                            <span class="fw-bold text-dark">1200,00 MAD</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="position-absolute bg-success rounded-circle shadow-lg" style="width: 80px; height: 80px; top: -20px; right: -20px; opacity: 0.8;"></div>
                        <div class="position-absolute bg-warning rounded-circle shadow-lg" style="width: 60px; height: 60px; bottom: -10px; left: -10px; opacity: 0.8;"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5 bg-light">
        <div class="container py-5">
            <div class="text-center mb-5">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-3">Fonctionnalités</span>
                <h2 class="display-5 fw-bold mb-3">Tout ce dont vous avez besoin</h2>
                <p class="lead text-muted">Une solution complète pour gérer vos frais de déplacement</p>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100 rounded-4">
                        <div class="card-body p-4">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                                <svg width="32" height="32" fill="currentColor" class="text-primary" viewBox="0 0 16 16">
                                    <path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h13zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13z"/>
                                    <path d="M3 5.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zM3 8a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9A.5.5 0 0 1 3 8zm0 2.5a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5z"/>
                                </svg>
                            </div>
                            <h4 class="fw-bold mb-3">Soumission facile</h4>
                            <p class="text-muted mb-0">Créez vos demandes de frais en quelques clics avec upload de justificatifs.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100 rounded-4">
                        <div class="card-body p-4">
                            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                                <svg width="32" height="32" fill="currentColor" class="text-success" viewBox="0 0 16 16">
                                    <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/>
                                </svg>
                            </div>
                            <h4 class="fw-bold mb-3">Validation rapide</h4>
                            <p class="text-muted mb-0">Les managers valident ou rejettent les demandes directement depuis l'interface.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100 rounded-4">
                        <div class="card-body p-4">
                            <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                                <svg width="32" height="32" fill="currentColor" class="text-warning" viewBox="0 0 16 16">
                                    <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                                    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
                                </svg>
                            </div>
                            <h4 class="fw-bold mb-3">Suivi en temps réel</h4>
                            <p class="text-muted mb-0">Consultez l'état de vos demandes : en cours, validée, rejetée.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100 rounded-4">
                        <div class="card-body p-4">
                            <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                                <svg width="32" height="32" fill="currentColor" class="text-info" viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                    <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                                </svg>
                            </div>
                            <h4 class="fw-bold mb-3">Historique complet</h4>
                            <p class="text-muted mb-0">Accédez à l'historique de tous vos remboursements et demandes passées.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100 rounded-4">
                        <div class="card-body p-4">
                            <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                                <svg width="32" height="32" fill="currentColor" class="text-danger" viewBox="0 0 16 16">
                                    <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                                </svg>
                            </div>
                            <h4 class="fw-bold mb-3">Sécurité garantie</h4>
                            <p class="text-muted mb-0">Vos données sont protégées avec un système d'authentification sécurisé.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100 rounded-4">
                        <div class="card-body p-4">
                            <div class="bg-secondary bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                                <svg width="32" height="32" fill="currentColor" class="text-secondary" viewBox="0 0 16 16">
                                    <path d="M1 2.5A1.5 1.5 0 0 1 2.5 1h3A1.5 1.5 0 0 1 7 2.5v3A1.5 1.5 0 0 1 5.5 7h-3A1.5 1.5 0 0 1 1 5.5v-3zm8 0A1.5 1.5 0 0 1 10.5 1h3A1.5 1.5 0 0 1 15 2.5v3A1.5 1.5 0 0 1 13.5 7h-3A1.5 1.5 0 0 1 9 5.5v-3zm-8 8A1.5 1.5 0 0 1 2.5 9h3A1.5 1.5 0 0 1 7 10.5v3A1.5 1.5 0 0 1 5.5 15h-3A1.5 1.5 0 0 1 1 13.5v-3zm8 0A1.5 1.5 0 0 1 10.5 9h3a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 13.5v-3z"/>
                                </svg>
                            </div>
                            <h4 class="fw-bold mb-3">Multi-rôles</h4>
                            <p class="text-muted mb-0">Espaces dédiés pour employés, managers et administrateurs.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Workflow Section -->
    <section id="workflow" class="py-5">
        <div class="container py-5">
            <div class="text-center mb-5">
                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill mb-3">Processus</span>
                <h2 class="display-5 fw-bold mb-3">Comment ça marche ?</h2>
                <p class="lead text-muted">Un workflow simplifié en 7 étapes</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="text-center">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow" style="width: 80px; height: 80px;">
                            <span class="fs-2 fw-bold">1</span>
                        </div>
                        <h5 class="fw-bold mb-2">Création</h5>
                        <p class="text-muted small">L'employé prépare sa demande de frais</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="text-center">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow" style="width: 80px; height: 80px;">
                            <span class="fs-2 fw-bold">2</span>
                        </div>
                        <h5 class="fw-bold mb-2">Soumission</h5>
                        <p class="text-muted small">Envoi vers le manager</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="text-center">
                        <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow" style="width: 80px; height: 80px;">
                            <span class="fs-2 fw-bold">3</span>
                        </div>
                        <h5 class="fw-bold mb-2">Validation</h5>
                        <p class="text-muted small">Le manager vérifie et valide</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="text-center">
                        <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow" style="width: 80px; height: 80px;">
                            <span class="fs-2 fw-bold">4</span>
                        </div>
                        <h5 class="fw-bold mb-2">Transfert</h5>
                        <p class="text-muted small">Transmission à l'administrateur</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="text-center">
                        <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow" style="width: 80px; height: 80px;">
                            <span class="fs-2 fw-bold">5</span>
                        </div>
                        <h5 class="fw-bold mb-2">Vérification</h5>
                        <p class="text-muted small">L'admin contrôle les justificatifs</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="text-center">
                        <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow" style="width: 80px; height: 80px;">
                            <span class="fs-2 fw-bold">6</span>
                        </div>
                        <h5 class="fw-bold mb-2">Approbation</h5>
                        <p class="text-muted small">Validation finale</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="text-center">
                        <div class="bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow" style="width: 80px; height: 80px;">
                            <span class="fs-2 fw-bold">7</span>
                        </div>
                        <h5 class="fw-bold mb-2">Notification</h5>
                        <p class="text-muted small">L'employé reçoit la confirmation</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="text-center">
                        <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow" style="width: 80px; height: 80px;">
                            <svg width="40" height="40" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/>
                            </svg>
                        </div>
                        <h5 class="fw-bold mb-2">Terminé</h5>
                        <p class="text-muted small">Demande traitée avec succès</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section id="benefits" class="py-5 bg-light">
        <div class="container py-5">
            <div class="text-center mb-5">
                <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill mb-3">Avantages</span>
                <h2 class="display-5 fw-bold mb-3">Pourquoi choisir ExpenseFlow ?</h2>
                <p class="lead text-muted">Les bénéfices pour votre entreprise</p>
            </div>
            <div class="row g-4 align-items-center">
                <div class="col-lg-6">
                    <div class="pe-lg-5">
                        <div class="d-flex mb-4">
                            <div class="flex-shrink-0 bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                <svg width="24" height="24" fill="currentColor" class="text-primary" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M10.854 8.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L7.5 10.793l2.646-2.647a.5.5 0 0 1 .708 0z"/>
                                    <path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1zm3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4h-3.5z"/>
                                </svg>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-2">Gain de temps considérable</h5>
                                <p class="text-muted mb-0">Réduisez jusqu'à 75% le temps de traitement des demandes de frais.</p>
                            </div>
                        </div>
                        <div class="d-flex mb-4">
                            <div class="flex-shrink-0 bg-success bg-opacity-10 rounded-circle p-3 me-3">
                                <svg width="24" height="24" fill="currentColor" class="text-success" viewBox="0 0 16 16">
                                    <path d="M1 3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1H1zm7 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/>
                                    <path d="M0 5a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1V5zm3 0a2 2 0 0 1-2 2v4a2 2 0 0 1 2 2h10a2 2 0 0 1 2-2V7a2 2 0 0 1-2-2H3z"/>
                                </svg>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-2">Transparence totale</h5>
                                <p class="text-muted mb-0">Suivi en temps réel de chaque demande pour tous les acteurs.</p>
                            </div>
                        </div>
                        <div class="d-flex mb-4">
                            <div class="flex-shrink-0 bg-warning bg-opacity-10 rounded-circle p-3 me-3">
                                <svg width="24" height="24" fill="currentColor" class="text-warning" viewBox="0 0 16 16">
                                    <path d="M1 11a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1v-3zm5-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7zm5-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1V2z"/>
                                </svg>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-2">Réduction des erreurs</h5>
                                <p class="text-muted mb-0">Éliminez les erreurs de saisie et les calculs manuels.</p>
                            </div>
                        </div>
                        <div class="d-flex">
                            <div class="flex-shrink-0 bg-info bg-opacity-10 rounded-circle p-3 me-3">
                                <svg width="24" height="24" fill="currentColor" class="text-info" viewBox="0 0 16 16">
                                    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-2">Conformité garantie</h5>
                                <p class="text-muted mb-0">Respectez les procédures et conservez une traçabilité complète.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                        <div class="card-body p-5 bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <div class="text-black">
                                <h3 class="fw-bold mb-4">Statistiques clés</h3>
                                <div class="row g-4">
                                    <div class="col-6">
                                        <div class="bg-white bg-opacity-10 rounded-3 p-4 text-center">
                                            <h2 class="display-4 fw-bold mb-2">98%</h2>
                                            <p class="mb-0">Taux de satisfaction</p>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="bg-white bg-opacity-10 rounded-3 p-4 text-center">
                                            <h2 class="display-4 fw-bold mb-2">-75%</h2>
                                            <p class="mb-0">Temps de traitement</p>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="bg-white bg-opacity-10 rounded-3 p-4 text-center">
                                            <h2 class="display-4 fw-bold mb-2">100%</h2>
                                            <p class="mb-0">Traçabilité</p>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="bg-white bg-opacity-10 rounded-3 p-4 text-center">
                                            <h2 class="display-4 fw-bold mb-2">24h</h2>
                                            <p class="mb-0">Délai moyen</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Users Section -->
    <section class="py-5">
        <div class="container py-5">
            <div class="text-center mb-5">
                <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill mb-3">Utilisateurs</span>
                <h2 class="display-5 fw-bold mb-3">Pour qui ?</h2>
                <p class="lead text-muted">Trois espaces dédiés pour chaque rôle</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
                        <div class="bg-primary text-white p-4 text-center">
                            <svg width="60" height="60" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3Zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                            </svg>
                        </div>
                        <div class="card-body p-4">
                            <h4 class="fw-bold mb-3">Employé</h4>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <svg width="20" height="20" fill="currentColor" class="text-success me-2" viewBox="0 0 16 16">
                                        <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/>
                                    </svg>
                                    Créer des demandes
                                </li>
                                <li class="mb-2">
                                    <svg width="20" height="20" fill="currentColor" class="text-success me-2" viewBox="0 0 16 16">
                                        <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/>
                                    </svg>
                                    Joindre justificatifs
                                </li>
                                <li class="mb-2">
                                    <svg width="20" height="20" fill="currentColor" class="text-success me-2" viewBox="0 0 16 16">
                                        <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/>
                                    </svg>
                                    Suivre les statuts
                                </li>
                                <li class="mb-2">
                                    <svg width="20" height="20" fill="currentColor" class="text-success me-2" viewBox="0 0 16 16">
                                        <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/>
                                    </svg>
                                    Consulter l'historique
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
                        <div class="bg-success text-white p-4 text-center">
                            <svg width="60" height="60" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8Zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022ZM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816ZM4.92 10A5.493 5.493 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0Zm3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z"/>
                            </svg>
                        </div>
                        <div class="card-body p-4">
                            <h4 class="fw-bold mb-3">Manager</h4>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <svg width="20" height="20" fill="currentColor" class="text-success me-2" viewBox="0 0 16 16">
                                        <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/>
                                    </svg>
                                    Voir demandes équipe
                                </li>
                                <li class="mb-2">
                                    <svg width="20" height="20" fill="currentColor" class="text-success me-2" viewBox="0 0 16 16">
                                        <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/>
                                    </svg>
                                    Consulter justificatifs
                                </li>
                                <li class="mb-2">
                                    <svg width="20" height="20" fill="currentColor" class="text-success me-2" viewBox="0 0 16 16">
                                        <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/>
                                    </svg>
                                    Valider / Rejeter
                                </li>
                                <li class="mb-2">
                                    <svg width="20" height="20" fill="currentColor" class="text-success me-2" viewBox="0 0 16 16">
                                        <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/>
                                    </svg>
                                    Filtrer par critères
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
                        <div class="bg-warning text-white p-4 text-center">
                            <svg width="60" height="60" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872l-.1-.34zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z"/>
                            </svg>
                        </div>
                        <div class="card-body p-4">
                            <h4 class="fw-bold mb-3">Administrateur</h4>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <svg width="20" height="20" fill="currentColor" class="text-success me-2" viewBox="0 0 16 16">
                                        <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/>
                                    </svg>
                                    Toutes les demandes
                                </li>
                                <li class="mb-2">
                                    <svg width="20" height="20" fill="currentColor" class="text-success me-2" viewBox="0 0 16 16">
                                        <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/>
                                    </svg>
                                    Validation finale
                                </li>
                                <li class="mb-2">
                                    <svg width="20" height="20" fill="currentColor" class="text-success me-2" viewBox="0 0 16 16">
                                        <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/>
                                    </svg>
                                    Gestion utilisateurs
                                </li>
                                <li class="mb-2">
                                    <svg width="20" height="20" fill="currentColor" class="text-success me-2" viewBox="0 0 16 16">
                                        <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/>
                                    </svg>
                                    Gestion catégories
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 position-relative overflow-hidden" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
        <div class="position-absolute w-100 h-100 top-0 start-0" style="opacity: 0.1;">
            <div class="position-absolute rounded-circle bg-white" style="width: 300px; height: 300px; top: -100px; left: -50px;"></div>
            <div class="position-absolute rounded-circle bg-white" style="width: 200px; height: 200px; bottom: -50px; right: -50px;"></div>
        </div>
        <div class="container py-5 position-relative">
            <div class="text-center text-white">
                <h2 class="display-4 fw-bold mb-4">Prêt à digitaliser vos frais ?</h2>
                <p class="lead mb-5 opacity-90">Rejoignez des centaines d'entreprises qui ont simplifié leur gestion</p>
                <div class="d-flex flex-wrap gap-3 justify-content-center">
                    <a href="/register" class="btn btn-light btn-lg rounded-pill px-5 py-3 fw-semibold shadow">
                        Créer un compte gratuitement
                    </a>
                    <a href="/login" class="btn btn-outline-light btn-lg rounded-pill px-5 py-3 fw-semibold">
                        Se connecter
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <div class="d-flex align-items-center mb-3">
                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none" class="me-2">
                            <rect width="40" height="40" rx="8" fill="url(#gradient2)"/>
                            <path d="M20 10 L30 15 L30 25 L20 30 L10 25 L10 15 Z" fill="white" opacity="0.9"/>
                            <circle cx="20" cy="20" r="5" fill="#667eea"/>
                            <defs>
                                <linearGradient id="gradient2" x1="0" y1="0" x2="40" y2="40">
                                    <stop offset="0%" style="stop-color:#667eea"/>
                                    <stop offset="100%" style="stop-color:#764ba2"/>
                                </linearGradient>
                            </defs>
                        </svg>
                        <span class="fw-bold fs-4">ExpenseFlow</span>
                    </div>
                    <p class="text-white-50 mb-0">La solution complète pour gérer vos frais de déplacement en toute simplicité.</p>
                </div>
                <div class="col-lg-2 col-md-4">
                    <h6 class="fw-bold mb-3">Produit</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#features" class="text-white-50 text-decoration-none">Fonctionnalités</a></li>
                        <li class="mb-2"><a href="#workflow" class="text-white-50 text-decoration-none">Processus</a></li>
                        <li class="mb-2"><a href="#benefits" class="text-white-50 text-decoration-none">Avantages</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-4">
                    <h6 class="fw-bold mb-3">Entreprise</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">À propos</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Contact</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Support</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-4">
                    <h6 class="fw-bold mb-3">Légal</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Confidentialité</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Conditions</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Cookies</a></li>
                    </ul>
                </div>
                <div class="col-lg-2">
                    <h6 class="fw-bold mb-3">Suivez-nous</h6>
                    <div class="d-flex gap-2">
                        <a href="#" class="btn btn-outline-light btn-sm rounded-circle p-2">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z"/>
                            </svg> 
                        </a>
                        <a href="#" class="btn btn-outline-light btn-sm rounded-circle p-2">
                             <!-- #region -->
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334 0-.14 0-.282-.006-.422A6.685 6.685 0 0 0 16 3.542a6.658 6.658 0 0 1-1.889.518 3.301 3.301 0 0 0 1.447-1.817 6.533 6.533 0 0 1-2.087.793A3.286 3.286 0 0 0 7.875 6.03a9.325 9.325 0 0 1-6.767-3.429 3.289 3.289 0 0 0 1.018 4.382A3.323 3.323 0 0 1 .64 6.575v.045a3.288 3.288 0 0 0 2.632 3.218 3.203 3.203 0 0 1-.865.115c-.212 0-.417-.02-.616-.058a3.283 3.283 0 0 0 3.067 2.277A6.588 6.588 0 0-1 .78l-.001-.045a9.344 9.344 0 0 0 5.026 1.465"/>
                            </svg>
                            <!-- #endregion --> 
                                                     </a>
</div></div>
            </div>
          </div>
        </footer>
        </body>
</html>