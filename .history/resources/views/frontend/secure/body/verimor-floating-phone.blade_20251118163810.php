{{-- TEST VERSİYONU --}}
<div style="position: fixed; bottom: 20px; right: 20px; z-index: 9999;">
    <button id="openFloatingPhone" style="
        width: 60px; 
        height: 60px; 
        border-radius: 50%; 
        background: red; 
        border: none; 
        color: white; 
        cursor: pointer;
        font-size: 24px;
    ">
        📞
    </button>
    <p style="background: yellow; padding: 10px; margin-top: 10px;">
        TEST: Widget yüklendi
    </p>
</div>

<script>
console.log('Verimor widget loaded!');
$(document).ready(function() {
    console.log('jQuery ready!');
    console.log('Button exists:', $('#openFloatingPhone').length);
});
</script>