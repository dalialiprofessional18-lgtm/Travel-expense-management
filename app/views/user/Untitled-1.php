<div class="user-card mb-3" 
                                         data-user-id="<?= $user['id'] ?>"
                                         data-user-name="<?= htmlspecialchars($user['nom']) ?>"
                                         data-user-email="<?= htmlspecialchars($user['email']) ?>"
                                         data-user-role="<?= $user['role'] ?>"
                                         data-user-avatar="<?= $user['avatar_url'] ?? '/assets/images/default-avatar.png' ?>"
                                         data-user-phone="<?= $user['telephone'] ?? 'Non renseigné' ?>"
                                         data-user-job="<?= $user['poste'] ?? 'Employé' ?>"
                                         data-user-department="<?= $user['departement'] ?? 'Non spécifié' ?>"
                                         onclick="selectUser(this)">
                                        
                                        <div class="d-flex align-items-center">
                                            <img src="<?= $user['avatar_url'] ?? '/assets/images/default-avatar.png' ?>" 
                                                 alt="<?= htmlspecialchars($user['nom']) ?>" 
                                                 class="user-avatar me-3">
                                            
                                            <div class="flex-grow-1">
                                                <div class="fw-bold"><?= htmlspecialchars($user['nom']) ?></div>
                                                <div class="small text-muted"><?= htmlspecialchars($user['email']) ?></div>
                                            </div>
                                            
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation(); viewUserProfile(this.closest('.user-card'))">
                                                    <i class="bi bi-eye"></i> Voir
                                                </button>
                                                <span class="badge bg-primary badge-role align-self-center">
                                                    <i class="bi bi-person me-1"></i>Employé
                                                </span>
                                            </div>
                                        </div>
                                    </div>