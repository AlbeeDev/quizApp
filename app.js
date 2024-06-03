document.addEventListener('DOMContentLoaded', function() {

    document.getElementById('question-image').addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('question-preview');
                img.src = e.target.result;
                img.style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    });
    
    document.querySelector('#image-input0').addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.querySelector('#image-preview0');
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    });

    document.querySelector('#format0').addEventListener('change', function(e) {
        let option = e.target.value;
        switch (option) {
            case 'image':
                if (document.querySelector('#answer0').style.display !== 'none') {
                    document.querySelector('#answer0').style.display = 'none';
                    document.querySelector('#image-container0').style.display = 'block';
                }
                break;
        
            default:
                if (document.querySelector('#image-container0').style.display !== 'none') {
                    document.querySelector('#image-container0').style.display = 'none';
                    document.querySelector('#answer0').style.display = 'block';
                }
                break;
        }
    });

    document.querySelector('#correct0').addEventListener('change', function(e) {
        const selectedValue = e.target.value;
        if (selectedValue === 'false') {
            e.target.classList.remove('btn-success');
            e.target.classList.add('btn-danger');
        } else {
            e.target.classList.remove('btn-danger');
            e.target.classList.add('btn-success');
        }
    });


    let answerCount = 1;
    document.getElementById('add-answer').addEventListener('click', function() {
        const container = document.getElementById('answers-container');
        if (container.children.length < 26) {
            const original = container.firstElementChild.cloneNode(true);
            original.querySelector('#format0').value = 'text';
            original.querySelector('#format0').id = 'format'+answerCount;
            original.querySelector('#answer0').value = '';
            original.querySelector('#answer0').style.display = 'block';
            original.querySelector('#answer0').id = 'answer'+answerCount;
            var imgelement = original.querySelector('#image-container0');
            imgelement.id='image-container'+answerCount;

            const labelelement= imgelement.querySelector('label[for="image-input0"]');
            labelelement.setAttribute('for','image-input'+answerCount)

            const inputelement= imgelement.querySelector('#image-input0');
            inputelement.id='image-input'+answerCount;
            imgelement.querySelector('#'+inputelement.id).value='';

            const previewelement = imgelement.querySelector('#image-preview0');
            previewelement.id='image-preview'+answerCount;
            imgelement.querySelector('#'+previewelement.id).src='';
            console.log('#'+inputelement.id+' '+'#'+previewelement.id)

            imgelement.style.display = 'none';
            
            console.log(answerCount)
            original.querySelector('#correct0').value = 'true';
            original.querySelector('#correct0').id='correct'+answerCount;
            container.appendChild(original);

            const elementemp = document.querySelector('#'+inputelement.id);
            if(elementemp) console.log('exists');
            document.querySelector('#'+inputelement.id).addEventListener('change', function() {
                console.log('DEBUGPRINT');
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.querySelector('#'+previewelement.id);
                        console.log('DEBUGPRINT');
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                }
            });

            document.querySelector('#format'+answerCount).addEventListener('change', function(e) {
                let localindex = this.id.substring(this.id.length - 1);
                let option = e.target.value;
                switch (option) {
                    case 'image':
                        if (document.querySelector('#answer'+localindex).style.display !== 'none') {
                            document.querySelector('#answer'+localindex).style.display = 'none';
                            document.querySelector('#image-container'+localindex).style.display = 'block';
                        }
                        break;
                
                    default:
                        if (document.querySelector('#image-container'+localindex).style.display !== 'none') {
                            document.querySelector('#image-container'+localindex).style.display = 'none';
                            document.querySelector('#answer'+localindex).style.display = 'block';
                        }
                        break;
                }
            });

            document.querySelector('#correct'+answerCount).addEventListener('change', function(e) {
                const selectedValue = e.target.value;
                if (selectedValue === 'false') {
                    e.target.classList.remove('btn-success');
                    e.target.classList.add('btn-danger');
                } else {
                    e.target.classList.remove('btn-danger');
                    e.target.classList.add('btn-success');
                }
            });

            answerCount++;
        }
        
    });

    document.getElementById('remove-answer').addEventListener('click', function() {
        const container = document.getElementById('answers-container');
        if (container.children.length > 1) {
            container.removeChild(container.lastElementChild);
        }
    });

    function logEvent(event) {
        const targetId = event.target.id ? `, ID: ${event.target.id}` : '';
    console.log(`Event: ${event.type}, Target: ${event.target.tagName}${targetId}, Time: ${new Date().toLocaleTimeString()}`);
    }
    
    // List of common events to monitor
    const commonEvents = [
        'click', 'dblclick', 'mousedown', 'mouseup', 'contextmenu',
        'keydown', 'keypress', 'keyup', 'focus', 'blur', 'change', 'input', 'submit', 'reset', 'resize',
        'drag', 'drop', 'dragstart', 'dragend', 'dragenter', 'dragleave', 'dragover',
        'touchstart', 'touchend', 'touchmove', 'touchcancel'
    ];
    
    // Attach a single event listener for each type using the capturing phase
    commonEvents.forEach(eventType => {
        document.addEventListener(eventType, logEvent, true);
    });
})