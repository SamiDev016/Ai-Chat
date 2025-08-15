@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6 text-center text-gray-800">💬 AI Chat</h1>

    <div class="bg-white shadow-lg rounded-xl p-6">
        
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-700">Conversation</h2>
            <button 
                id="clear-chat" 
                class="bg-red-500 hover:bg-red-600 text-white text-sm px-4 py-2 rounded-lg transition">
                Clear Chat
            </button>
        </div>

        <div id="chat-history" class="mb-4 space-y-4 max-h-[400px] overflow-y-auto">
        </div>

        <form id="ai-form" class="flex gap-3">
            @csrf
            <input 
                type="text" 
                name="question" 
                id="question" 
                placeholder="Type your question..."
                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                required
            >
            <button 
                type="submit" 
                class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2 rounded-lg transition">
                Ask
            </button>
        </form>
    </div>
</div>

<script>
    function loadHistory() {
        let chat = JSON.parse(localStorage.getItem('chat') || '[]');
        $('#chat-history').html('');
        chat.forEach(item => {
            $('#chat-history').append(`
                <div>
                    <p class="font-semibold text-indigo-700 bg-green-200 p-2">You:</p>
                    <p class="mb-2 bg-green-200 p-2">${item.question}</p>
                    <p class="font-semibold text-green-700 bg-blue-200 p-2">AI:</p>
                    <p class="mb-4 whitespace-pre-wrap bg-blue-200 p-2">${item.answer}</p>
                </div>
            `);
        });
        $('#chat-history').scrollTop($('#chat-history')[0].scrollHeight);
    }

    $('#ai-form').on('submit', function(e) {
        e.preventDefault();

        let question = $('#question').val();
        $('#question').val('');

        let chat = JSON.parse(localStorage.getItem('chat') || '[]');
        chat.push({ question: question, answer: null });

        $.ajax({
            url: "{{ route('ask.ai') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                chat: chat
            },
            success: function(response) {
                chat[chat.length - 1].answer = response.answer;
                localStorage.setItem('chat', JSON.stringify(chat));
                loadHistory();
            },
            error: function() {
                alert('Error: Could not get AI response.');
            }
        });
    });

    $('#clear-chat').on('click', function() {
        if (confirm('Are you sure you want to clear the chat history?')) {
            localStorage.removeItem('chat');
            loadHistory();
        }
    });

    loadHistory();
</script>
@endsection
