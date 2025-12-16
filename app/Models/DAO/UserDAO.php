<?php
namespace App\Models\DAO;
use App\Models\Entities\User;
use App\Config\Database;
use PDO;
require_once __DIR__ . '/../Entities/User.php';

class UserDAO {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }
    // عدد المستخدمين تحت إشراف مدير معين
    
public function findTeamMembers(int $managerId): array
{
    $sql = "SELECT * FROM users WHERE manager_id = ? ORDER BY nom ASC";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$managerId]);
    
    $users = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $users[] = new User(
            $row['id'],
            $row['nom'],
            $row['email'],
            $row['password'],
            $row['role'],
            $row['manager_id'] ?? null,
            $row['created_at'] ?? null
        );
    }
    return $users;
}

public function countTeamMembers(int $managerId): int
{
    $sql = "SELECT COUNT(*) FROM users WHERE manager_id = ?";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$managerId]);
    return (int) $stmt->fetchColumn();
}

public function findManagerOf(int $userId): ?User
{
    $sql = "SELECT u2.* 
            FROM users u1
            JOIN users u2 ON u1.manager_id = u2.id
            WHERE u1.id = ?";
    
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$userId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$row) return null;
    
    return new User(
        $row['id'],
        $row['nom'],
        $row['email'],
        $row['password'],
        $row['role'],
        $row['manager_id'] ?? null,
        $row['created_at'] ?? null
    );
}
    public function countUnderManager( int $managerId ): int {
        $sql = 'SELECT COUNT(*) FROM users WHERE manager_id = ?';
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute( [ $managerId ] );
        return ( int )$stmt->fetchColumn();
    }

    public function update( User $user ): bool {
        $sql = "
        UPDATE users 
        SET 
            nom = ?,
            email = ?,
            role = ?,
            password = ?,           
            job_title = ?,
            experience_details = ?,
            manager_id = ?,
            avatar_path = ?,
            avatar_mime = ?,
            avatar_size = ?,
            cover_path = ?,
            cover_mime = ?,
            cover_size = ?,
            updated_at = NOW()
        WHERE id = ?
    ";

        $stmt = $this->pdo->prepare( $sql );

        return $stmt->execute( [
            $user->getNom(),
            $user->getEmail(),
            $user->getRole(),
            $user->getPassword(),
            $user->getJobTitle(),
            $user->getExperienceDetails(),           // يكون مشفر بالـ password_hash
            $user->getManagerId(),
            $user->getAvatarPath(),
            $user->getAvatarMime(),
            $user->getAvatarSize(),
            $user->getCoverPath(),
            $user->getCoverMime(),
            $user->getCoverSize(),
            $user->getId()
        ] );
    }
    // عدد كل المستخدمين

    public function updateProfileImage( int $userId, string $type, string $path ): bool {
        $field = $type === 'avatar' ? 'avatar_path' : 'cover_path';
        $sql = "UPDATE users SET $field = ? WHERE id = ?";
        $stmt = $this->pdo->prepare( $sql );
        return $stmt->execute( [ $path, $userId ] );
    }

    public function updateAvatar( int $userId, string $url=null ): bool {
        $sql = 'UPDATE users SET avatar_path = ? WHERE id = ?';
        $stmt = $this->pdo->prepare( $sql );
        return $stmt->execute( [ $url, $userId ] );
    }

    public function updateCover( int $userId, string $url=null ): bool {
        $sql = 'UPDATE users SET cover_path = ? WHERE id = ?';
        $stmt = $this->pdo->prepare( $sql );
        return $stmt->execute( [ $url, $userId ] );
    }

/**
 * ✅ NOUVELLE MÉTHODE : Mettre à jour UNIQUEMENT le profil (sans toucher manager_id, role, password)
 */
public function updateProfileOnly(User $user): bool {
    $sql = "UPDATE users 
            SET nom = ?,
                email = ?,
                job_title = ?,
                experience_details = ?,
                updated_at = NOW()
            WHERE id = ?";
    
    $stmt = $this->pdo->prepare($sql);
    
    return $stmt->execute([
        $user->getNom(),
        $user->getEmail(),
        $user->getJobTitle(),
        $user->getExperienceDetails(),
        $user->getId()
    ]);
}

    public function findByUserId( int $userId, int $limit = 10 ): array {
        $sql = 'SELECT * FROM deplacements WHERE user_id = ? ORDER BY created_at DESC LIMIT ?';

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute( [ $userId, $limit ] );

        $results = [];
        while ( $row = $stmt->fetch( \PDO::FETCH_ASSOC ) ) {
            $results[] = new User(
                $row[ 'id' ],
                $row[ 'nom' ],
                $row[ 'email' ],
                $row[ 'role' ],
                $row[ 'statut' ],
                $row[ 'password' ],           // صحيح
                $row[ 'manager_id' ],
                $row[ 'job_title' ],
                $row[ 'experience_details' ],
                $row[ 'created_at' ] ?? null,
                $row[ 'updated_at' ] ?? null
            );
        }

        return $results;
    }

    public function countAll(): int {
        return ( int )$this->pdo->query( 'SELECT COUNT(*) FROM users' )->fetchColumn();
    }

    public function findByRole( $roles ): array {
        // لو جالك دور واحد → حوّله لمصفوفة
        if ( !is_array( $roles ) ) {
            $roles = [ $roles ];
        }

        // تحويل المصفوفة إلى placeholders للـ SQL ( مثل: ?, ?, ? )
        $placeholders = str_repeat( '?,', count( $roles ) - 1 ) . '?';

        $sql = "SELECT * FROM users WHERE role IN ($placeholders) ORDER BY nom ASC";
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute( $roles );

        $users = [];
        while ( $row = $stmt->fetch( \PDO::FETCH_ASSOC ) ) {
            $users[] =          new User(
                $row[ 'id' ],
                $row[ 'nom' ],
                $row[ 'email' ],
                $row[ 'role' ],
                $row[ 'statut' ],
                $row[ 'password' ],           // صحيح
                $row[ 'manager_id' ],
                $row[ 'job_title' ],
                $row[ 'experience_details' ],
                $row[ 'created_at' ] ?? null,
                $row[ 'updated_at' ] ?? null
            );
        }

        return $users;
    }

    public function findById( $id ) {
        $sql = 'SELECT * FROM users WHERE id = ?';
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute( [ $id ] );
        $row = $stmt->fetch( \PDO::FETCH_ASSOC );

        if ( !$row ) return null;

        return new User(
            $row[ 'id' ],
            $row[ 'nom' ],
            $row[ 'email' ],
            $row[ 'role' ],
            $row[ 'statut' ],
            $row[ 'password' ],
            $row[ 'is_verified' ],           // صحيح
            $row[ 'manager_id' ],
            $row[ 'job_title' ],
            $row[ 'experience_details' ],
            $row[ 'created_at' ] ?? null,
            $row[ 'updated_at' ] ?? null
        );
    }

    public function findByIdForProfile( $id ) {
        $sql = 'SELECT * FROM users WHERE id = ?';
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute( [ $id ] );
        $row = $stmt->fetch( \PDO::FETCH_ASSOC );

        if ( !$row ) return null;

        return User::constructFull(
            $row[ 'id' ],
            $row[ 'nom' ],
            $row[ 'email' ],
            $row[ 'role' ],

            $row[ 'password' ],           // صحيح
            $row[ 'manager_id' ],                      // صحيح
            $row[ 'avatar_path' ],            $row[ 'avatar_mime' ],
            $row[ 'avatar_size' ],           // صحيح
            $row[ 'cover_path' ],
            $row[ 'cover_mime' ] ,
            $row[ 'cover_size' ],            $row[ 'created_at' ] ?? null,
            $row[ 'updated_at' ] ?? null
        );
    }

    public function findByEmail( $email ) {
        $sql = 'SELECT * FROM users WHERE email = ?';
        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute( [ $email ] );
        $row = $stmt->fetch( \PDO::FETCH_ASSOC );

        if ( !$row ) return null;

        return new User(
            $row[ 'id' ],
            $row[ 'nom' ],
            $row[ 'email' ],
            $row[ 'role' ],
            $row[ 'statut' ],

            $row[ 'password' ],           // صحيح
            $row[ 'manager_id' ],
            $row[ 'job_title' ],
            $row[ 'experience_details' ],
            $row[ 'created_at' ] ?? null,
            $row[ 'updated_at' ] ?? null
        );
    }



/**
 * Mettre à jour les préférences d'apparence
 */
public function updatePreferences(int $userId, string $theme, string $language): bool {
    $sql = 'UPDATE users SET theme = ?, language = ?, updated_at = NOW() WHERE id = ?';
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute([$theme, $language, $userId]);
}

/**
 * Mettre à jour les préférences de notifications
 */
public function updateNotificationPreferences(int $userId, array $prefs): bool {
    $sql = 'UPDATE users SET 
            email_notifications = ?, 
            note_validated_notif = ?, 
            note_rejected_notif = ?, 
            new_trip_notif = ?,
            updated_at = NOW() 
            WHERE id = ?';
    
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute([
        $prefs['email_notifications'] ?? 1,
        $prefs['note_validated'] ?? 1,
        $prefs['note_rejected'] ?? 1,
        $prefs['new_trip'] ?? 1,
        $userId
    ]);
}

    public function findAll() {
        $sql = 'SELECT * FROM users ORDER BY nom ASC';
        $rows = $this->pdo->query( $sql )->fetchAll( \PDO::FETCH_ASSOC );
        $list = [];
        foreach ( $rows as $r ) {
            $list[] =  new User(
                $r[ 'id' ],
                $r[ 'nom' ],
                $r[ 'email' ],
                $r[ 'role' ],
                $r[ 'statut' ],

                $r[ 'password' ],           // صحيح
                $r[ 'is_verified' ],           // صحيح
                $r[ 'manager_id' ],
                $r[ 'job_title' ],
                $r[ 'experience_details' ],
                $r[ 'created_at' ] ?? null,
                $r[ 'updated_at' ] ?? null
            );
        }
        return $list;
    }

    public function findAllP() {
        $sql = 'SELECT * FROM users ORDER BY nom ASC';
        $rows = $this->pdo->query( $sql )->fetchAll( \PDO::FETCH_ASSOC );
        $list = [];
        foreach ( $rows as $row ) {
            $list[] =          User::constructFull(
                $row[ 'id' ],
                $row[ 'nom' ],
                $row[ 'email' ],
                $row[ 'role' ],
                $row[ 'password' ],           // صحيح
                $row[ 'manager_id' ],                      // صحيح
                $row[ 'avatar_path' ],            $row[ 'avatar_mime' ],
                $row[ 'avatar_size' ],           // صحيح
                $row[ 'cover_path' ],
                $row[ 'cover_mime' ] ,
                $row[ 'cover_size' ],            $row[ 'created_at' ] ?? null,
                $row[ 'updated_at' ] ?? null
            );
        }
        return $list;
    }

    public function countRequestsThisMonth( int $userId ): int {
        $sql = "SELECT COUNT(*) FROM notes_frais nf
            JOIN deplacements d ON nf.deplacement_id = d.id
            WHERE nf.user_id = ? 
              AND MONTH(d.date_depart) = MONTH(CURDATE())
              AND YEAR(d.date_depart) = YEAR(CURDATE())";

        $stmt = $this->pdo->prepare( $sql );
        $stmt->execute( [ $userId ] );
        return ( int )$stmt->fetchColumn();
    }

    public function insert( User $u ) {
        $sql = 'INSERT INTO users (nom, email, role, password, manager_id ,statut) VALUES (?, ?, ?, ?, ?, ?)';
        $stmt = $this->pdo->prepare( $sql );
        return $stmt->execute( [
            $u->getNom(), $u->getEmail(), $u->getRole(), $u->getPassword(), $u->getManagerId(), $u->getStatut()
        ] );
    }
public function existsByEmail($email)
{
    $sql = "SELECT id FROM users WHERE email = ?";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$email]);
    return $stmt->fetch() !== false;
}

    public function updateRole( $id, $role ) {
        $sql = 'UPDATE users SET role = ? WHERE id = ?';
        $stmt = $this->pdo->prepare( $sql );
        return $stmt->execute( [ $role, $id ] );
    }
    /**
 * جلب المستخدم (الموظف) من خلال معرّف الـ déplacement
 * مثالية لصفحة /managerVoir/{id}
 */
public function findUserByDeplacementId( $deplacementId)
{
    $sql = "
        SELECT u.*, m.nom AS manager_nom
        FROM users u
        JOIN deplacements d ON u.id = d.user_id
        LEFT JOIN users m ON u.manager_id = m.id
        WHERE d.id = ?
        LIMIT 1
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$deplacementId]);
    $row = $stmt->fetch(\PDO::FETCH_ASSOC );

    if (!$row) {
        return null;
    }

    $user = new User(
        $row['id'],
        $row['nom'],
        $row['email'],
        $row['role'],
        $row['statut'],
        $row['password'],
        $row['manager_id'] ?? null
    );
    // إضافة الصور لو موجودة
    
        $user->setAvatar($row['avatar_path'], $row['avatar_mime'], $row['avatar_size']);

        $user->setCover($row['cover_path'], $row['cover_mime'], $row['cover_size']);

    return $user;
}

    public function delete( $id ) {
        $sql = 'DELETE FROM users WHERE id = ?';
        $stmt = $this->pdo->prepare( $sql );
        return $stmt->execute( [ $id ] );
    }
}