<captcha-challenge
    data-key="{{ config('captcha.turnstile.id')  }}"
></captcha-challenge>

@error('cf-turnstile-response')
<p class="text-sm text-destructive">{{ $message }}</p>
@enderror
