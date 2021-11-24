// Load file front from scss
require('../scss/front.scss');
require('../../node_modules/tinymce/tinymce');
require('../../node_modules/tinymce/themes/silver/index');
require('../../node_modules/tinymce/icons/default/index');

import '@popperjs/core'
import { modifierPhases } from '@popperjs/core';
import { createPopper } from '@popperjs/core/lib/createPopper';
import 'bootstrap';
import tinymce from '../../node_modules/tinymce/tinymce';

//Init MCE
tinymce.init({
    selector: 'textarea.wysiwyg',
    
  });

  window.addEventListener('load', () => {
    let searchAddress = document.querySelector('#registration_form_address');

    if(searchAddress !== null)
    {
        let addrSelect = document.querySelector('#address_results');
        let url = addrSelect.getAttribute('data-url');
        searchAddress.addEventListener('change', () =>{
            let xhttp = new XMLHttpRequest();
            xhttp.open('GET', url + "?search=" + searchAddress.value);
            addrSelect.innerHTML = "";
            
            xhttp.onload = () => {
                let results = JSON.parse(xhttp.responseText);
                console.log(results);
                for (const address of results[0].features) {
                    let option = document.createElement('option');
                    option.innerHTML = option.value = address.properties.label;
                    addrSelect.appendChild(option);

                }
            };

            xhttp.send();
        });

        addrSelect.addEventListener('change', () => {
            searchAddress.value = addrSelect.value;
        });

    }
});