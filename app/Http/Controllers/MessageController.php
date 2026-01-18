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
    public function index()
    {
        $messages = $this->messages->getPublished();
        return view('messages.index', compact('messages'));
    }

    // Formulario nuevo mensaje
    public function showNewForm()
    {
        return view('messages.new_message');
    }

    // Guardar nuevo mensaje
public function create(Request $request)
{
    // Validación básica de campos
    $data = $request->validate([
        'subject' => 'required|string',
        'text' => 'required|string|max:280',
    ]);

    // Normalizamos los valores
    $subject = trim($data['subject']);
    $text = trim($data['text']);
    $author = session('user.name');

    // Lista de palabrotas
    $badWords = [
        'puta','puto','gilipollas','mierda','hostia','joder',
        'coño','cabrón','cabron','idiota','imbécil','imbecil',
        'subnormal', 'hijo de puta', 'maricon','maricón',
        'perra', 'cara polla', 'comemierda'
    ];

    // Comprobaciones de seguridad
    $isDangerous = preg_match('/<script|onerror=|onload=|onclick=|onmouseover=|javascript:|eval\(|iframe|embed|object/i', $text);

    $containsBadWord = false;
    foreach ($badWords as $word) {
        if (stripos($text, $word) !== false) {
            $containsBadWord = true;
            break;
        }
    }

    // Determinar estado
    $status = (!$isDangerous && !$containsBadWord)
        ? 'pending'   // caso normal → pendiente de moderación
        : 'rejected'; // caso prohibido → rechazado directamente

    // Crear el mensaje
    $message = new Message(
        id: uniqid(),
        author: $author,
        subject: $subject,
        text: $text,
        status: $status,
        createdAt: date('Y-m-d H:i:s')
    );

    $this->messages->save($message->toArray());

    // Respuesta visual
    if ($status === 'pending') {
        return redirect('/')
            ->with('success', 'Tu mensaje está pendiente de moderación.');
    } else {
        return redirect('/')
            ->with('success', 'El mensaje contenía contenido no permitido y ha sido enviado al historial de mensajes rechazados.');
    }
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

    public function invalid()
    {
        $rejectedMessages = $this->messages->getRejected();
        return view('messages.invalid_messages', compact('rejectedMessages'));
    }
}