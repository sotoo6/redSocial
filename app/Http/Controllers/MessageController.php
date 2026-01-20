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
        'perra', 'cara polla', 'comemierda', 'polla'
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

    // Si el mensaje es válido → mostrar en la misma vista
    if ($status === 'pending') {
        return view('messages.new_message', [
            'messagePending' => $message->toArray(),
            'subjects' => ['Desarrollo Web Entorno Servidor', 'Digitalización', 'Despliegue', 'Inglés', 'Empresa']
        ]);
    } else {
        // Si el mensaje fue rechazado, también mostrarlo
        return view('messages.new_message', [
            'messagePending' => $message->toArray(),
            'subjects' => ['Desarrollo Web Entorno Servidor', 'Digitalización', 'Despliegue', 'Inglés', 'Empresa'],
            'rejected' => true
        ]);
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