<?php
class Formulaire
{
    const LIMIT_USERNAME = 3;
    const LIMIT_MESSAGE = 20;
    public $username;
    public $sujet;
    public $email;
    public $message;
    public $offre;

    public function __construct(string $username, int $sujet, string $email, string $message, string $offre = null)
    {
        $this->username = $username;
        $this->sujet = $sujet;
        $this->email = $email;
        $this->message = $message;
        $this->offre = $offre;
    }

    public function isEmpty(): bool
    { 
        return empty($this->getErrors());
    }
    public function getErrors(): array
    {
        $errors = [];
        if(strlen($this->username) < self::LIMIT_USERNAME) {  
            $errors['username'] = 'Votre nom d\'utilisateur est trop court.';
        }
        if(empty($this->username)) {
            $errors['username'] = 'Votre nom d\'utilisateur est requis.';
        }
        if(!is_numeric($this->sujet)) {
            $errors['sujet'] = 'Sujet invalide.';
        }
        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'L\'adresse email ' . htmlspecialchars($this->email, ENT_QUOTES) . ' est invalide.';
        }
        if(empty($this->email)) {
            $errors['email'] = 'Votre adresse email est requise.';
        } 
        if(strlen($this->message) < self::LIMIT_MESSAGE) {
            $errors['message'] = 'Votre message est trop court.';
        }
        if(empty($this->message)) {
            $errors['message'] = 'Votre message est requis.';
        } 
    return $errors;
}

        public function emailit(int $id)
        {
            $message = '<table bgcolor="#F2F2F2" width="100%"><table style="max-width:800px;border-collapse:collapse;min-height:100px;height:100px;" width="100%" cellpadding="0" cellspacing="0" border="0" align="center"><tr bgcolor="#701818" height="120px" align="center"><td><a href="https://www.promo-parrain.com"><img alt="Promo-Parrain.com" src="https://www.promo-parrain.com/images/logo.png" /></a></td></tr>';
            $message.= '<tr bgcolor="#FFF"><td cellpadding="0" cellspacing="0" border="0" style="padding:0px 20px;"><h2 style="color:#181B1F;">Un nouveau message de contact sur Promo-Parrain</h2></td></tr>';
            $message.= '<tr bgcolor="#FFF"><td style="padding:4px 20px;"><p style="color:#181B1F;">Bonjour Admin, <br />Tu as reçu un nouveau message sur promo-parrain, pour le consulter cliquer ici :  : </p><br />';
            $message.= '<a style="color:#701818;font-weight:bold;font-size:16px;" href="https://www.promo-parrain.com/membres/admin/messagesvoir.php?message='.$id.'">Voir le message</a></td></tr>';
            $message.= '<tr><td bgcolor="#FFF"><br /></td></tr>';
            $message.= '<tr height="100px" bgcolor="#701818" style="border-spacing: 20px 10px;min-height:100px;" align="center"><td colspan="3"><a style="color:#FFF;" href="https://www.promo-parrain.com">Copyright Promo-parrain.com</a></td></tr>';
            $message.= '<tr><td bgcolor="#FFF"><br /></td></tr>';
            $message.= '</table></table>';
            $headers = "From: Promo-Parrain.com <admin@promo-parrain.com>\r\n".
                  "Reply-To: Promo-Parrain.com <no-reply@promo-parrain.com>\r\n".
                  "MIME-Version: 1.0" . "\r\n" .
                  "Content-type: text/html; charset=UTF-8" . "\r\n";

            mail('admin@promo-parrain.com', 'Un nouveau message de contact sur Promo-parrain', $message, $headers);
            return 'mail envoyé';
        }
}
?>