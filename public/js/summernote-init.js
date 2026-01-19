/**
 * Summernote Auto-Initializer
 * يُفعّل محرر Summernote تلقائياً على أي textarea بها class "richtext" أو "summernote"
 * 
 * الاستخدام:
 * <textarea class="richtext" name="content"></textarea>
 * أو
 * <textarea class="summernote" name="description"></textarea>
 * 
 * خيارات إضافية عبر data attributes:
 * data-height="400" - تحديد ارتفاع المحرر
 * data-placeholder="اكتب هنا..." - نص توضيحي
 * data-simple="true" - شريط أدوات مبسط
 */

$(document).ready(function() {
    // التحقق من وجود Summernote
    if (typeof $.fn.summernote === 'undefined') {
        console.warn('Summernote is not loaded');
        return;
    }

    // الخطوط العربية المتاحة
    const arabicFonts = [
        'Cairo', 'Tajawal', 'Amiri', 'Almarai', 
        'Noto Naskh Arabic', 'Scheherazade New',
        'Arial', 'Times New Roman', 'Tahoma', 'Verdana'
    ];

    // أحجام الخطوط
    const fontSizes = ['8', '9', '10', '11', '12', '14', '16', '18', '20', '22', '24', '28', '32', '36', '48'];

    // شريط الأدوات الكامل
    const fullToolbar = [
        ['style', ['style']],
        ['font', ['fontname', 'fontsize', 'bold', 'italic', 'underline', 'strikethrough', 'clear']],
        ['color', ['forecolor', 'backcolor']],
        ['para', ['ul', 'ol', 'paragraph', 'height']],
        ['table', ['table']],
        ['insert', ['link', 'picture', 'hr']],
        ['view', ['fullscreen', 'codeview', 'help']],
        ['misc', ['undo', 'redo']]
    ];

    // شريط أدوات مبسط
    const simpleToolbar = [
        ['font', ['bold', 'italic', 'underline']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['insert', ['link']],
        ['view', ['fullscreen']],
        ['misc', ['undo', 'redo']]
    ];

    // الأنماط المتاحة
    const styleTags = [
        { tag: 'p', title: 'فقرة', value: 'p' },
        { tag: 'h1', title: 'عنوان 1', value: 'h1' },
        { tag: 'h2', title: 'عنوان 2', value: 'h2' },
        { tag: 'h3', title: 'عنوان 3', value: 'h3' },
        { tag: 'h4', title: 'عنوان 4', value: 'h4' },
        { tag: 'blockquote', title: 'اقتباس', value: 'blockquote' }
    ];

    // دالة تهيئة Summernote
    function initSummernote($element) {
        // تجنب التهيئة المزدوجة
        if ($element.hasClass('summernote-initialized')) {
            return;
        }

        // قراءة الخيارات من data attributes
        const height = $element.data('height') || 300;
        const placeholder = $element.data('placeholder') || 'اكتب هنا...';
        const isSimple = $element.data('simple') === true || $element.data('simple') === 'true';

        // إعدادات Summernote
        const options = {
            lang: 'ar-AR',
            height: height,
            direction: 'rtl',
            placeholder: placeholder,
            toolbar: isSimple ? simpleToolbar : fullToolbar,
            fontNames: arabicFonts,
            fontNamesIgnoreCheck: arabicFonts,
            fontSizes: fontSizes,
            styleTags: styleTags,
            tabDisable: true,
            dialogsInBody: true,
            callbacks: {
                onInit: function() {
                    // تطبيق الخط الافتراضي
                    $(this).next('.note-editor').find('.note-editable').css({
                        'font-family': 'Cairo, Tajawal, sans-serif',
                        'font-size': '16px',
                        'line-height': '1.9',
                        'direction': 'rtl',
                        'text-align': 'right'
                    });
                },
                onImageUpload: function(files) {
                    // يمكن إضافة رفع الصور للسيرفر هنا
                    // حالياً يتم تحويل الصورة لـ base64
                    const reader = new FileReader();
                    reader.onloadend = function() {
                        const img = $('<img>').attr('src', reader.result);
                        $element.summernote('insertNode', img[0]);
                    };
                    reader.readAsDataURL(files[0]);
                }
            }
        };

        // تفعيل Summernote
        $element.summernote(options);
        $element.addClass('summernote-initialized');
    }

    // البحث عن جميع العناصر المستهدفة وتفعيل المحرر
    const selectors = '.richtext, .summernote, textarea[data-editor="richtext"], textarea[data-editor="summernote"]';
    
    $(selectors).each(function() {
        initSummernote($(this));
    });

    // دعم التحميل الديناميكي (للعناصر المضافة لاحقاً)
    // يمكن استدعاء هذه الدالة بعد إضافة عناصر جديدة
    window.initRichTextEditors = function() {
        $(selectors).each(function() {
            if (!$(this).hasClass('summernote-initialized')) {
                initSummernote($(this));
            }
        });
    };

    // دالة لتدمير المحرر (مفيدة عند إزالة العناصر)
    window.destroyRichTextEditor = function(selector) {
        $(selector).each(function() {
            if ($(this).hasClass('summernote-initialized')) {
                $(this).summernote('destroy');
                $(this).removeClass('summernote-initialized');
            }
        });
    };

    console.log('✅ Summernote Rich Text Editor initialized');
});
