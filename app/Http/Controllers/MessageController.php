<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contracts\IMessageRepository;
use App\Exceptions\DatabaseUnavailableException;

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

        try {
            $published = $this->messages->getPublished();
        } catch (DatabaseUnavailableException $e) {
            return view('messages.index', [
                'messages' => [],
                'subjects' => $subjects,
                'dbError'  => $e->getMessage(),
            ]);
        }

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

        $idUser = session('user.idUser');

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

        try {
            $this->messages->save($payload);
        } catch (DatabaseUnavailableException $e) {
            return back()->with('status', $e->getMessage());
        }

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
        try {
            $pendingMessages = $this->messages->getPending();
        } catch (DatabaseUnavailableException $e) {
            return view('messages.moderation', [
                'pendingMessages' => [],
                'dbError'         => $e->getMessage(),
            ]);
        }

        return view('messages.moderation', compact('pendingMessages'));
    }

    // Aprobar mensaje
    public function approve($id)
    {
        try {
            $this->messages->approve($id);
        } catch (DatabaseUnavailableException $e) {
            return redirect('/moderation')->with('status', $e->getMessage());
        }

        return redirect('/moderation');
    }

    // Rechazar mensaje
    public function reject($id)
    {
        try {
            $this->messages->reject($id);
        } catch (DatabaseUnavailableException $e) {
            return redirect('/moderation')->with('status', $e->getMessage());
        }

        return redirect('/moderation');
    }

    // Borrar mensaje
    public function delete(Request $request, $id)
    {
        try {
            $msg = $this->messages->find((string)$id);
        } catch (DatabaseUnavailableException $e) {
            return redirect('/')->with('status', $e->getMessage());
        }

        if (!$msg) {
            return redirect('/')->with('status', 'Mensaje no encontrado.');
        }

        $currentIdUser = session('user.idUser');
        if (!$currentIdUser) {
            return redirect('/login')->with('status', 'Debes iniciar sesión.');
        }

        // Permisos: en BD, lo correcto es comprobar por idUser
        if ((string)($msg['idUser'] ?? '') !== (string)$currentIdUser) {
            return redirect('/')->with('status', 'No tienes permisos para borrar este mensaje.');
        }

        try {
            $this->messages->delete($id);
        } catch (DatabaseUnavailableException $e) {
            return redirect('/')->with('status', $e->getMessage());
        }

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

        try {
            $msg = $this->messages->find($id);
        } catch (DatabaseUnavailableException $e) {
            return redirect('/')->with('status', $e->getMessage());
        }

        if (!$msg) {
            return redirect('/')->with('error', 'El mensaje no existe.');
        }

        $currentIdUser = session('user.idUser');
        if (!$currentIdUser) {
            return redirect('/login')->with('status', 'Debes iniciar sesión.');
        }

        if ((string)($msg['idUser'] ?? '') !== (string)$currentIdUser) {
            return redirect('/')->with('error', 'No tienes permisos para modificar este mensaje.');
        }

        $payload = [
            'id'      => $id,
            'idUser'  => $msg['idUser'] ?? $currentIdUser,
            'subject' => $subject,
            'content' => $text,
            'status'  => 'pending',
        ];

        try {
            $this->messages->update($payload);
        } catch (DatabaseUnavailableException $e) {
            return redirect('/')->with('status', $e->getMessage());
        }

        return redirect('/')->with('status', 'Tu mensaje actualizado está pendiente de moderación.');
    }

    public function invalid()
    {
        try {
            $rejectedMessages = $this->messages->getRejected();
        } catch (DatabaseUnavailableException $e) {
            return view('messages.invalid_messages', [
                'rejectedMessages' => [],
                'dbError'          => $e->getMessage(),
            ]);
        }

        return view('messages.invalid_messages', compact('rejectedMessages'));
    }
}
