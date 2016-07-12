<?php
if (!defined('included'))
{
    die;
}

class Usuario
{
    public $user;
    public $id;
    private $reciente;

    public function __construct()
    {
        global $db;

        if (isset($_COOKIE[account_cookie]))
        {
            $token = preg_replace("/[^a-zA-Z0-9]/", "", $_COOKIE[account_cookie]);

            $sql = $db->query("
                SELECT
                    user_id
                FROM `sesiones`
                WHERE
                    token = '{$token}'
                LIMIT 1
            ");

            if ($sesion = $sql->fetch_object())
            {
                $id = $sesion->user_id;

                // TODO: No se si la Id del usuario es `codigo` o hay que agregar una columna
                $g = $db->query("
                    SELECT
                        u.usuario AS usuario,
                        u.nombre AS nombre,
                        u.tipo AS tipo,
                        d.avatar AS avatar,
                        d.domicilio AS domicilio,
                        d.cuit AS cuit,
                        u.email AS email
                    FROM `users` AS u
                    LEFT JOIN `user_data` AS d ON(
                        d.codigo = u.id
                    )
                    WHERE
                        u.id = '{$id}'
                    LIMIT 1
                ");

                if ($u = $g->fetch_object())
                {
                    $this->user = array(
                        'usuario'   => $u->usuario,
                        'tipo'      => $u->tipo,
                        'email'     => $u->email,
                        'avatar'    => $u->avatar,
                        'cuit'      => $u->cuit,
                        'nombre'    => $u->nombre,
                        'domicilio' => $u->domicilio
                    );

                    $this->id = $sesion->user_id;
                }
                else
                {
                    @setcookie(account_cookie, '', time()-60*60*24*365);
                }
            }
        }
        else
        {
            if (isset($_COOKIE[account_reciente]))
            {
                $this->reciente = true;
            }
        }
    }

    public function es_reciente()
    {
        return $this->reciente;
    }

    public function logout()
    {
        global $db;

        if (!isset($_COOKIE[account_cookie]))
        {
            return true;
        }

        $cookie = parse('text', $_COOKIE[account_cookie]);

        $db->query("
            DELETE FROM `sesiones` WHERE token = '{$cookie}' LIMIT 1
        ");

        if (!headers_sent())
        {
            setcookie(account_cookie, '', time()-60*60*24*365);
            setcookie(account_reciente, '', time()-60*60*24*365);
        }

        return true;
    }
}
?>
