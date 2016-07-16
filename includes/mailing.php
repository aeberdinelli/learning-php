<?php
if (!defined('included'))
{
    die;
}

class Mailing
{
    private $lang;
    private $subject;
    private $body;
    private $to;
    private $headers;
    private $is_prepared = false;

    private $settings = array(
        'from'      => 'Grupo Esmar <info@esmar.com.ar>',
        'reply-to'  => 'Grupo Esmar <clientes@esmar.com.ar>'
    );

    public function __construct($email, $type = 'html', $lang = 'es')
    {
        global $idioma, $plugins;

        $plugins->ejecutar("empieza_constructor_mailing");

        $this->to = $email;

        if ($lang != '')
        {
            $this->lang = $lang;
        }
        else
        {
            $this->lang = $idioma->actual;
        }

        $content_type = ($type == 'html') ? 'text/html' : 'text/plain';

        $this->add_header("From", $this->settings['from']);
        $this->add_header("Reply-To", $this->settings['reply-to']);
        $this->add_header("Mime-Version", "1.0");
        $this->add_header("Content-Type", $content_type);

        $plugins->ejecutar("termina_constructor_mailing");
    }

    public function add_header($header, $val)
    {
        global $plugins;

        $plugins->ejecutar("empieza_agregar_header_mail");

        $this->headers .= "{$header}: {$val}\n";

        $plugins->ejecutar("termina_agregar_header_mail");
    }

    public function preparar($var, $replace = array())
    {
        global $db, $plugins;

        $plugins->ejecutar("empieza_preparacion_mail");

        if (preg_match('/[0-9]+/', $var))
        {
            // Is an Id
            $sql = $db->query("
                SELECT
                    subject,
                    body
                FROM `mailing`
                WHERE
                    id = '{$var}'
                LIMIT 1
            ");

            if ($s = $sql->fetch_object())
            {
                $plugins->ejecutar("mail_obtenido");

                $this->subject  = $s->subject;
                $this->body     = $s->body;
            }
            else
            {
                $plugins->ejecutar("mail_no_encontrado");

                throw new Exception("Email template not found");
            }
        }
        else
        {
            $sql = $db->query("
                SELECT
                    subject,
                    body
                FROM `mailing`
                WHERE
                    codename = '{$var}'
                    AND
                    (
                        lang LIKE '%\"".$this->lang."\"%'
                        OR lang LIKE \"%'".$this->lang."'%\"
                    )
                LIMIT 1
            ");

            if ($s = $sql->fetch_object())
            {
                $plugins->ejecutar("mail_obtenido");

                $this->subject  = $s->subject;
                $this->body     = $s->body;
            }
            else
            {
                $plugins->ejecutar("mail_no_encontrado");

                throw new Exception("Email template not found");
            }
        }

        if (es_assoc($replace))
        {
            foreach ($replace as $buscar => $reemplazar)
            {
                $plugins->ejecutar("empieza_reemplazo_mail_assoc");
                $this->body = str_replace('[['.$buscar.']]', $reemplazar, $this->body);

                $plugins->ejecutar("termina_reemplazo_mail_assoc");
            }
        }
        else
        {
            for ($i = 0;$i < count($replace);$i++)
            {
                $plugins->ejecutar("empieza_reemplazo_mail");
                $this->body = str_replace("%".($i + 1), $replace[$i], $this->body);

                $plugins->ejecutar("termina_reemplazo_mail");
            }

            // Borrar escapes \1, \2, etc
            $this->body = preg_replace('#%\\\([0-9]+)#', '%\\1', $this->body);

            $this->is_prepared = true;
        }

        $plugins->ejecutar("termina_preparacion_mail");
    }

    public function enviar()
    {
        global $plugins;

        $plugins->ejecutar("empiza_envio_mail");
        mail($this->to, $this->subject, $this->body, $this->headers);

        $plugins->ejecutar("termina_envio_mail");
        return true;
    }
}
?>
