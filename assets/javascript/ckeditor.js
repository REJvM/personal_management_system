import {
    BalloonEditor,
    AccessibilityHelp,
    Autoformat,
    AutoLink,
    Autosave,
    BlockQuote,
    BlockToolbar,
    Bold,
    Code,
    CodeBlock,
    Essentials,
    FindAndReplace,
    Heading,
    Highlight,
    Indent,
    IndentBlock,
    Italic,
    Link,
    List,
    ListProperties,
    Paragraph,
    SelectAll,
    SpecialCharacters,
    SpecialCharactersArrows,
    SpecialCharactersCurrency,
    SpecialCharactersEssentials,
    SpecialCharactersLatin,
    SpecialCharactersMathematical,
    SpecialCharactersText,
    Strikethrough,
    Table,
    TableToolbar,
    TextTransformation,
    Underline,
    Undo,
} from "https://cdn.ckeditor.com/ckeditor5/43.3.1/ckeditor5.js";

/* get editor div to retrieve data attributes */
const editorBlock = document.querySelector("div#editor");

const editorConfig = {
    toolbar: {
        items: [
            "undo",
            "redo",
            "|",
            "findAndReplace",
            "|",
            "heading",
            "|",
            "bold",
            "italic",
            "underline",
            "strikethrough",
            "code",
            "|",
            "specialCharacters",
            "link",
            "insertTable",
            "highlight",
            "blockQuote",
            "codeBlock",
            "|",
            "bulletedList",
            "numberedList",
            "outdent",
            "indent",
        ],
        shouldNotGroupWhenFull: false,
    },
    plugins: [
        AccessibilityHelp,
        Autoformat,
        AutoLink,
        Autosave,
        BlockQuote,
        BlockToolbar,
        Bold,
        Code,
        CodeBlock,
        Essentials,
        FindAndReplace,
        Heading,
        Highlight,
        Indent,
        IndentBlock,
        Italic,
        Link,
        List,
        ListProperties,
        Paragraph,
        SelectAll,
        SpecialCharacters,
        SpecialCharactersArrows,
        SpecialCharactersCurrency,
        SpecialCharactersEssentials,
        SpecialCharactersLatin,
        SpecialCharactersMathematical,
        SpecialCharactersText,
        Strikethrough,
        Table,
        TableToolbar,
        TextTransformation,
        Underline,
        Undo,
    ],
    codeBlock: {
        languages: [
            { language: "plaintext", label: "Plain text" },
            { language: "css", label: "CSS" },
            { language: "html", label: "HTML" },
            { language: "javascript", label: "JavaScript" },
            { language: "php", label: "PHP" },
            { language: "python", label: "Python" },
            { language: "ruby", label: "Ruby" },
            { language: "typescript", label: "TypeScript" },
            { language: "xml", label: "XML" },
        ],
    },
    blockToolbar: [
        "bold",
        "italic",
        "|",
        "link",
        "insertTable",
        "|",
        "bulletedList",
        "numberedList",
        "outdent",
        "indent",
    ],
    heading: {
        options: [
            {
                model: "paragraph",
                title: "Paragraph",
                class: "ck-heading_paragraph",
            },
            {
                model: "heading2",
                view: "h2",
                title: "Heading 2",
                class: "ck-heading_heading2",
            },
            {
                model: "heading3",
                view: "h3",
                title: "Heading 3",
                class: "ck-heading_heading3",
            },
            {
                model: "heading4",
                view: "h4",
                title: "Heading 4",
                class: "ck-heading_heading4",
            },
            {
                model: "heading5",
                view: "h5",
                title: "Heading 5",
                class: "ck-heading_heading5",
            },
            {
                model: "heading6",
                view: "h6",
                title: "Heading 6",
                class: "ck-heading_heading6",
            },
        ],
    },
    initialData: editorBlock.dataset.initialdata,
    link: {
        addTargetToExternalLinks: true,
        defaultProtocol: "https://",
        decorators: {
            toggleDownloadable: {
                mode: "manual",
                label: "Downloadable",
                attributes: {
                    download: "file",
                },
            },
        },
    },
    list: {
        properties: {
            styles: true,
            startIndex: true,
            reversed: true,
        },
    },
    placeholder: "Type or paste your content here!",
    table: {
        contentToolbar: ["tableColumn", "tableRow", "mergeTableCells"],
    },
};

BalloonEditor.create(document.querySelector("#editor"), editorConfig)
    .then((newEditor) => {
        editor = newEditor;
    })
    .catch((error) => {
        console.error(error);
    });

const form = document.querySelector("form[name='blog_post']");
/* when form is submitted */
form.addEventListener("submit", (event) => {
    event.preventDefault();
    /* Add contents of ckeditor to the hidden field to submit with the form. */
    const editorData = editor.getData();
    document.querySelector("input#editor").value = editorData;
    form.submit();
});
