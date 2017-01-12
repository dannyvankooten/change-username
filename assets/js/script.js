(function() {
    'use strict';

    var opts = change_username;
    var timeout;
    var mount = document.querySelector('.user-user-login-wrap td .description');
    var currentUsernameInput = document.getElementById('user_login');
    var link = el('a', { href: '#', onclick: toggle }, 'Change' );
    var newUsernameInput = el('input', { type: "text", name: "new_user_login", value: currentUsernameInput.value, className: "regular-text" , style: { "min-height": "28px" }, autocomplete: "off" });
    var submitButton = el('input', { type: "button", value: "Change", className: "button", onclick: onSubmit });
    var form = el('form', {
        method: "POST",
        onsubmit: onSubmit,
        style: { display: "none" }
    }, [
        newUsernameInput,
        submitButton
    ]);
    var message = el('p', { style: {
        display: 'none'
    }});

    mount.parentNode.replaceChild(link, mount);
    link.parentNode.appendChild(el('div', [ form, message ]));

    /**
     * @param object
     * @param attrs
     */
    function setAttributes(object, attrs) {
        for(var key in attrs) {
            if(typeof(attrs[key]) === "object") {
                setAttributes(object[key], attrs[key]);
            } else {
                object[key] = attrs[key];
            }
        }
    }
    /**
     *
     * @param name
     * @param attrs
     * @param children
     * @returns {Element}
     */
    function el(name, attrs, children) {
        var e = document.createElement(name);

        if( !children && ( Array.isArray(attrs) || typeof(attrs) === "string")) {
            children = attrs;
            attrs = null;
        }

        if( attrs) {
            setAttributes(e, attrs);
        }

        if( children) {
            if(typeof(children) === "string") {
                e.textContent = children;
            } else {
               for(var i=0; i<children.length; i++) {
                e.appendChild(children[i]);
               }
            }
        }

        return e;
    }

    function onSubmit(e) {
        e.preventDefault();

        var new_username = form.new_user_login.value;
        var current_username = currentUsernameInput.value;

        // do nothing if username is very short or unchanged
        if( new_username.length < 2 || new_username === current_username ) {
            return;
        }

        submitButton.value = "Please wait..";
        submitButton.disabled = true;
        var data = 'current_username='+encodeURIComponent(current_username)+'&new_username='+encodeURIComponent(new_username)+'&_ajax_nonce='+encodeURIComponent(opts.nonce);
        var request = new XMLHttpRequest();
        request.open('POST', opts.ajaxurl + "?action=change_username", true);
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        request.onload = function() {
            submitButton.value = "Change";
            submitButton.disabled = false;
            var errored = true;
            if (request.status >= 200 && request.status < 400 && request.responseText != -1 ) {
                try {
                    var data = JSON.parse(request.responseText);

                    // we're good.
                    errored = false;

                    // update nonce
                    opts.nonce = data.new_nonce;

                    // show response message
                    message.style.color = data.success ? 'green' : 'red';
                    message.innerHTML = data.message;

                    if( data.success ) {
                        currentUsernameInput.value = new_username;
                        toggle();
                    }
                } catch(e) {}
            }

            if(errored) {
                message.style.color = 'red';
                message.textContent = "Uh oh, something went wrong submitting the form.";
            }

            // clear message timeout & set a new one
            if(timeout) {
                window.clearTimeout(timeout);
            }
            message.style.display = '';
            timeout = window.setTimeout(function() { message.style.display = 'none'; }, 6000);
        };
        request.send(data);
    }

    function toggleOnEscape(e) {
         if(e.keyCode == 27 ) { toggle(); }
    }

    // toggle between link / form
    function toggle(e) {
        if(e) {
            e.preventDefault();
        }

        if( form.style.display === 'none' ) {
            form.style.display = '';
            link.style.display = 'none';
            currentUsernameInput.style.display = 'none';
            newUsernameInput.focus();
            document.addEventListener('keydown', toggleOnEscape);
        } else {
            form.style.display = 'none';
            link.style.display = 'inline';
            currentUsernameInput.style.display = '';
            document.removeEventListener('keydown', toggleOnEscape);
        }
    }
})();