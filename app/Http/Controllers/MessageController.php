<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contracts\IMessageRepository;
use App\Models\Message;

class MessageController extends Controller
{
    private IMessageRepository $messages;

    public function __construct(IMessageRepository $messages)
    {
        $this->messages = $messages;
    }

    // Mostrar mensajes publicados (home)
    public function index(Request $request)
    {
        $subject = $request->query('subject', 'todas');

        $subjects = [
            'Desarrollo Web Entorno Servidor',
            'Desarrollo Web Entorno Cliente',
            'Diseño Interfaces',
            'Despliegue',
            'Digitalización',
            'Itinerario Personal',
            'Inglés',
            'Afondamiento',
        ];

        $published = $this->messages->getPublished();

        $messages = ($subject === 'todas')
            ? $published
            : array_values(array_filter($published, fn($m) => ($m['subject'] ?? '') === $subject));

        return view('messages.index', compact('messages', 'subjects'));
    }

    // Formulario nuevo mensaje
    public function showNewForm()
    {
        return view('messages.new_message');
    }

    // Guardar nuevo mensaje
    public function create(Request $request)
    {
        $data = $request->validate([
            'subject' => 'required|string',
            'text'    => 'required|string|max:280',
        ]);

        $subject = trim($data['subject']);
        $text    = trim($data['text']);

        $idUser  = session('user.idUser');

        if (!$idUser) {
            return redirect('/login')->with('status', 'Debes iniciar sesión para publicar.');
        }

        $badWords = [
            'puta','puto','gilipollas','mierda','hostia','joder',
            'coño','cabrón','cabron','idiota','imbécil','imbecil',
            'subnormal', 'hijo de puta', 'maricon','maricón',
            'perra', 'cara polla', 'comemierda', 'polla'
        ];

        $isDangerous = preg_match('/<script|onerror=|onload=|onclick=|onmouseover=|javascript:|eval\(|iframe|embed|object/i', $text);

        $containsBadWord = false;
        foreach ($badWords as $word) {
            if (stripos($text, $word) !== false) {
                $containsBadWord = true;
                break;
            }
        }

        $status = (!$isDangerous && !$containsBadWord) ? 'pending' : 'rejected';

        $payload = [
            'idUser'    => $idUser,
            'subject'   => $subject,
            'content'   => $text,
            'status'    => $status,
            'createdAt' => date('Y-m-d H:i:s'),
        ];

        $this->messages->save($payload);

        return redirect('/')->with(
            'status',
            $status === 'pending'
                ? 'Mensaje enviado (pendiente de moderación).'
                : 'Mensaje rechazado automáticamente.'
        );
    }


    // Cola de moderación
    public function moderation()
    {
        $pendingMessages = $this->messages->getPending();
        return view('messages.moderation', compact('pendingMessages'));
    }

    // Aprobar mensaje
    public function approve($id)
    {
        $this->messages->approve($id);
        return redirect('/moderation');
    }

    // Rechazar mensaje
    public function reject($id)
    {
        $this->messages->reject($id);
        return redirect('/moderation');
    }

    // Borrar mensaje
    public function delete(Request $request, $id)
    {
        $msg = $this->messages->find((string)$id);

        if (!$msg) {
            return redirect('/')->with('status', 'Mensaje no encontrado.');
        }

        $currentUserName = session('user.name');

        // Solo permite borrar si el mensaje es suyo
        if (($msg['author'] ?? '') !== $currentUserName) {
            return redirect('/')->with('status', 'No tienes permisos para borrar este mensaje.');
        }

        $this->messages->delete($id);

        return redirect('/')->with('status', 'Mensaje borrado correctamente.');
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'id'      => 'required|string',
            'subject' => 'required|string',
            'text'    => 'required|string|max:280',
        ]);

        $id      = (string) $data['id'];
        $subject = trim($data['subject']);
        $text    = trim($data['text']);

        $msg = $this->messages->find($id);
        if (!$msg) {
            return redirect('/')->with('error', 'El mensaje no existe.');
        }

        // Comprobación de permisos: en DB lo correcto es por idUser
        $currentIdUser = session('user.idUser');
        if (!$currentIdUser) {
            return redirect('/login')->with('status', 'Debes iniciar sesión.');
        }

        if ((string)($msg['idUser'] ?? '') !== (string)$currentIdUser) {
            return redirect('/')->with('error', 'No tienes permisos para modificar este mensaje.');
        }

        // Actualizamos campos (en DB el texto es "content")
        $payload = [
            'id'      => $id,
            'idUser'  => $msg['idUser'] ?? $currentIdUser,
            'subject' => $subject,
            'content' => $text,      // <-- clave correcta para BD
            'status'  => 'pending',  // al editar vuelve a moderación
        ];

        $this->messages->update($payload);

        return redirect('/')->with('status', 'Tu mensaje actualizado está pendiente de moderación.');
    }

    public function invalid()
    {
        $rejectedMessages = $this->messages->getRejected();
        return view('messages.invalid_messages', compact('rejectedMessages'));
    }
}