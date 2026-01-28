<div style="font-family: ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto, Ubuntu, Cantarell, 'Noto Sans', 'Helvetica Neue', Arial, 'Apple Color Emoji', 'Segoe UI Emoji'; line-height:1.5; color:#111827;">

    <h2 style="margin:0 0 12px; font-size:20px;">New Contact Us Message</h2>

    <p style="margin:0 0 16px;">You've received a new message from the website contact form.</p>

    <h3 style="margin:16px 0 8px; font-size:16px;">Contact Information</h3>
    <ul style="margin:0 0 16px; padding-left:18px;">
        <li><strong>Name:</strong> {{ $name }}</li>
        <li><strong>Email:</strong> {{ $email }}</li>
        @if(!empty($phone))
            <li><strong>Phone:</strong> {{ $phone }}</li>
        @endif
        <li><strong>Subject:</strong> {{ $subjectLine }}</li>
    </ul>

    <h3 style="margin:16px 0 8px; font-size:16px;">Message</h3>
    <div style="white-space:pre-wrap; background:#F9FAFB; border:1px solid #E5E7EB; border-radius:8px; padding:12px;">
        {{ $messageBody }}
    </div>
</div>
