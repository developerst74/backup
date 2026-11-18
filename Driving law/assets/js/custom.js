document.addEventListener("DOMContentLoaded", function () {

    const button = document.querySelector(".view-more-btn");

    const extraCards = document.querySelector(".bottom-cards");

    if (!button || !extraCards) return;

    button.addEventListener("click", function (e) {

        e.preventDefault();

        extraCards.classList.toggle("show");

        if (extraCards.classList.contains("show")) {

            button.textContent = "View Less";

        } else {

            button.textContent = "View More";

        }

    });

});


(function () {

    'use strict';

    document.addEventListener('click', function (e) {

        const button = e.target.closest(

            '.diving-dipper-main .view-more-btn a'

        );

        if (!button) return;

        e.preventDefault();

        const section = button.closest('.diving-dipper-main');

        if (!section) return;

        section.classList.toggle('is-expanded');

        const isOpen = section.classList.contains('is-expanded');

        const text = button.querySelector('.elementor-button-text');

        if (text) {

            text.textContent = isOpen

                ? 'Show less'

                : 'Read the full guide';

        }

    });

})();

jQuery(function ($) {

    "use strict";

    const DESCRIPTIONS = {

        Lower:

            "On these answers, jail is less likely — first-time offenders with a clean history are generally not imprisoned. But it's never automatic: the right submissions, references and a QTOP course are what secure the fine-plus-2-year-disqualification outcome rather than something worse.",

        Moderate:

            "There are real aggravating factors here, so jail is a genuine possibility the court will weigh. This is exactly the situation where experienced representation makes the difference — pointing the court to community service or a suspended sentence, and to the 2-year minimum disqualification.",

        High:

            "With a prior disqualified driving offence and multiple aggravating factors, the risk of actual imprisonment is significant. The court starts from a position that previous penalties were insufficient. Experienced representation is critical to build a comprehensive case in mitigation to seek an intensive community order or suspended term."

    };

    $(".condition-tabs").each(function () {

        const $root = $(this);

        function getPanels() {

            return $root.find(".e-n-tabs-content > div");

        }

        function setRiskText($panel, text, cssClass) {

            const $jailBox = $panel.find("#injective-jail, #indicative-jail");

            // Hide static duplicate widgets

            $jailBox.children(".elementor-widget-text-editor, #null, #moderate, #lower, #high")

                .not(".dynamic-risk-display")

                .hide();

            let $display = $jailBox.find(".dynamic-risk-display");

            if (!$display.length) {

                $display = $('<div class="elementor-widget-text-editor dynamic-risk-display"><p>–</p></div>');

                $jailBox.append($display);

            }

            $display.show().find("p")

                .text(text)

                .attr("class", cssClass);

        }

        function getDescriptionBox($panel) {

            let $description = $panel.find(".condtion-result-descrption");

            if (!$description.length) {

                $description = $('<div class="condtion-result-descrption"></div>');

                $panel.append($description);

            }

            return $description;

        }

        /*
    
        LIVE DYNAMIC UPDATE (Pehle click se hi change hoga)
    
        */

        function updateResult($panel) {

            if (!$panel.length) {

                return;

            }

            const yesCount = $panel.find(".btn-active-yes").length;

            const noCount = $panel.find(".btn-active-no").length;

            const answered = yesCount + noCount;

            const $description = getDescriptionBox($panel);

            // Agar user ne abhi tak koi bhi button click nahi kiya

            if (answered === 0) {

                setRiskText($panel, "–", "risk-color-null");

                $description.hide().empty().removeClass("risk-lower risk-moderate risk-high");

                return;

            }

            const panelIndex = getPanels().index($panel);

            const isFirstOffence = panelIndex === 0;

            let risk = "Moderate";

            let descClass = "risk-moderate";

            if (isFirstOffence) {

                // TAB 1: First Offence ("Yes — my first")

                // Agar 2 ya 3 YES hain -> Moderate, warna -> Lower

                if (yesCount >= 2) {

                    risk = "Moderate";

                    descClass = "risk-moderate";

                } else {

                    risk = "Lower";

                    descClass = "risk-lower";

                }

            } else {

                // TAB 2: Repeat Offence ("No — I've been charged before")

                // Agar 2 ya 3 YES hain -> High, warna -> Moderate

                if (yesCount >= 2) {

                    risk = "High";

                    descClass = "risk-high";

                } else {

                    risk = "Moderate";

                    descClass = "risk-moderate";

                }

            }

            // Update single text inside #injective-jail

            const textClass = (risk === "Lower") ? "risk-lower" : (risk === "High") ? "risk-high" : "risk-moderate";

            setRiskText($panel, risk, textClass);

            // Instantly show description & change smoothly

            $description

                .removeClass("risk-lower risk-moderate risk-high")
                .addClass(descClass)
                .html("<p>" + DESCRIPTIONS[risk] + "</p>")
                .stop(true, true)
                .fadeIn(200);

        }

        /*
    
        BUTTON CLICK
    
        */

        $root.on("click", ".elementor-widget-button .elementor-button", function (e) {

            e.preventDefault();

            const $button = $(this);

            const text = $.trim($button.text()).toLowerCase();

            if (text !== "yes" && text !== "no") {

                return;

            }

            const $group = $button.closest(".e-con:has(> .elementor-widget-button), .e-con");
            $group

                .find(".elementor-widget-button .elementor-button")
                .removeClass("btn-active-yes btn-active-no");
            if (text === "yes") {
                $button.addClass("btn-active-yes");
            } else {
                $button.addClass("btn-active-no");

            }

            const $panel = $button.closest(".e-n-tabs-content > div");
            updateResult($panel);

        });

        /*
    
        TAB SWITCH
    
        */

        $root.on("click", ".e-n-tab-title", function () {
            const $tab = $(this);
            setTimeout(function () {
                const index = $tab.index();
                const $panel = getPanels().eq(index);
                updateResult($panel);
            }, 100);
        });

        // Initial load

        getPanels().each(function () {
            updateResult($(this));
        });

    });

});


// DRINK-DRIVING PAGE - QUESTION RESULT SYSTEM

document.addEventListener("DOMContentLoaded", function () {
    const answers = {

        q1: null,
        q2: null,
        q3: null
    };

    const resultBox = document.querySelector(".licence-result");

    // =========================================
    // CHECK RESULT BOX
    // =========================================
    if (!resultBox) {
        console.warn("Result box .licence-result not found.");
        return;
    }
    // =========================================
    // SELECT BUTTON
    // =========================================

    function selectButton(button, type) {
        const question = button.closest(".question-row");
        if (!question) return;


        // Remove previous selected state

        question

            .querySelectorAll(".answer-btn")
            .forEach(function (btn) {
                btn.classList.remove(
                    "selected-yes",
                    "selected-no"
                );

            });


        // Add selected state

        if (type === "yes") {
            button.classList.add("selected-yes");
        } else {
            button.classList.add("selected-no");
        }

    }

    // HIDE RESULT

    function hideResult() {

        resultBox.classList.remove(
            "show-result",
            "result-success",
            "result-warning",
            "result-danger"

        );
        resultBox.innerHTML = "";
    }



    // SHOW RESULT

    function showResult(message, resultType) {
        resultBox.innerHTML = message;
        resultBox.classList.remove(

            "result-success",
            "result-warning",
            "result-danger"
        );
        resultBox.classList.add(
            resultType,
            "show-result"
        );

    }


    // UPDATE RESULT

    function updateResult() {
        // Count how many questions are answered
        const answeredCount = Object.values(answers).filter(
            function (value) {
                return value !== null;
            }
        ).length;

        // LESS THAN 3 ANSWERS

        // RESULT MUST STAY HIDDEN

        if (answeredCount < 3) {
            hideResult();
            return;
        }

        // =====================================
        // Q1 = NO
        // HIGH RANGE
        // =====================================
        if (answers.q1 === "no") {
            showResult(
                "A reading of 0.15 or more is high range — no work licence is available at any court. Preparation still decides how far past the 6-month minimum your disqualification goes. Call 1300 952 255, or read our high range guide.",
                "result-danger"
            );
            return;
        }

        // Q1 YES + Q2 NO

        if (
            answers.q1 === "yes" &&
            answers.q2 === "no"
        ) {
            showResult(
                "You may not qualify — a work licence needs an open licence and a clean recent record. Edge cases exist, so call 1300 952 255 before ruling it out.",
                "result-warning"
            );
            return;
        }

        // Q1 YES + Q2 YES + Q3 NO

        if (
            answers.q1 === "yes" &&
            answers.q2 === "yes" &&
            answers.q3 === "no"
        ) {
            showResult(
                "You may not qualify — a work licence needs an open licence and a clean recent record. Edge cases exist, so call 1300 952 255 before ruling it out.",
                "result-warning"
            );
            return;
        }

        // Q1 YES + Q2 YES + Q3 YES
        if (
            answers.q1 === "yes" &&
            answers.q2 === "yes" &&
            answers.q3 === "yes"
        ) {
            showResult(
                "You look eligible to apply — but it must be filed before you are sentenced. Call 1300 952 255 before your court date so nothing is lost.",
                "result-success"
            );
            return;
        }

        // FALLBACK
        hideResult();
    }

    // Q1 YES

    document
        .querySelector(".q1-yes")
        ?.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
            answers.q1 = "yes";
            selectButton(this, "yes");
            updateResult();
        });

    // Q1 NO

    document
        .querySelector(".q1-no")
        ?.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
            answers.q1 = "no";
            selectButton(this, "no");
            updateResult();
        });

    // Q2 YES

    document
        .querySelector(".q2-yes")
        ?.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
            answers.q2 = "yes";
            selectButton(this, "yes");
            updateResult();
        });

    // Q2 NO

    document
        .querySelector(".q2-no")
        ?.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
            answers.q2 = "no";
            selectButton(this, "no");
            updateResult();
        });


    // Q3 YES

    document
        .querySelector(".q3-yes")
        ?.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
            answers.q3 = "yes";
            selectButton(this, "yes");
            updateResult();
        });


    // Q3 NO
    document
        .querySelector(".q3-no")
        ?.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
            answers.q3 = "no";
            selectButton(this, "no");
            updateResult();
        });


    // INITIAL STATE

    hideResult();
    console.log("Drink-driving question system loaded.");
});

document.addEventListener('click', function (e) {
    const result = e.target.closest('a[href*="elementor-action"]');
    if (!result) return;
    e.preventDefault();
    e.stopPropagation();

    // Details container show
    const box = document.querySelector('#court-result');
    if (box) {
        box.style.setProperty('display', 'block', 'important');
    }

    // Search live results hide
    const searchResults = result.closest('.e-search-results');
    if (searchResults) {
        searchResults.style.setProperty('display', 'none', 'important');
    }
}, true);




// =========================================
// WORK LICENCE PAGE - 4 QUESTION SYSTEM
// =========================================

document.addEventListener("DOMContentLoaded", function () {

    const answers = {
        q1: null,
        q2: null,
        q3: null,
        q4: null
    };

    const resultBox = document.querySelector(".work-licence-result");

    // =========================================
    // CHECK RESULT BOX
    // =========================================

    if (!resultBox) {
        console.warn("Result box .work-licence-result not found.");
        return;
    }

    // =========================================
    // SELECT BUTTON
    // =========================================

    function selectButton(button, type) {

        const question = button.closest(".wl-question-row");
        if (!question) return;

        question.querySelectorAll(".wl-answer-btn").forEach(function (btn) {
            btn.classList.remove(
                "wl-selected-yes",
                "wl-selected-no"
            );
        });

        if (type === "yes") {
            button.classList.add("wl-selected-yes");
        } else {
            button.classList.add("wl-selected-no");
        }
    }

    // =========================================
    // HIDE RESULT
    // =========================================

    function hideResult() {

        resultBox.classList.remove(
            "wl-show-result",
            "wl-result-success",
            "wl-result-warning",
            "wl-result-danger"
        );

        resultBox.innerHTML = "";
    }

    // =========================================
    // SHOW RESULT
    // =========================================

    function showResult(message, resultType) {

        resultBox.innerHTML = message;

        resultBox.classList.remove(
            "wl-result-success",
            "wl-result-warning",
            "wl-result-danger"
        );

        resultBox.classList.add(
            resultType,
            "wl-show-result"
        );
    }

    // =========================================
    // UPDATE RESULT
    // =========================================

    function updateResult() {

        const answeredCount = Object.values(answers).filter(function (value) {
            return value !== null;
        }).length;

        // Wait until all 4 questions are answered

        if (answeredCount < 4) {
            hideResult();
            return;
        }

        // ALL YES

        if (
            answers.q1 === "yes" &&
            answers.q2 === "yes" &&
            answers.q3 === "yes" &&
            answers.q4 === "yes"
        ) {

            showResult(
                "You look eligible to apply. A work licence must be granted before you're sentenced — so don't plead guilty first. Call 1300 952 255 and we'll get your application ready.",
                "wl-result-success"
            );

            return;
        }

        // ANY NO

        showResult(
            "A work licence is granted where losing your licence would cause you or your family extreme hardship — usually loss of income. If you don't need to drive for work it's harder, but call us; it's often more arguable than people assume.",
            "wl-result-warning"
        );
    }

    // =========================================
    // Q1 YES
    // =========================================

    document.querySelector(".wl-q1-yes")?.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();

        answers.q1 = "yes";

        selectButton(this, "yes");

        updateResult();
    });

    // =========================================
    // Q1 NO
    // =========================================

    document.querySelector(".wl-q1-no")?.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();

        answers.q1 = "no";

        selectButton(this, "no");

        updateResult();
    });

    // =========================================
    // Q2 YES
    // =========================================

    document.querySelector(".wl-q2-yes")?.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();

        answers.q2 = "yes";

        selectButton(this, "yes");

        updateResult();
    });

    // =========================================
    // Q2 NO
    // =========================================

    document.querySelector(".wl-q2-no")?.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();

        answers.q2 = "no";

        selectButton(this, "no");

        updateResult();
    });

    // =========================================
    // Q3 YES
    // =========================================

    document.querySelector(".wl-q3-yes")?.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();

        answers.q3 = "yes";

        selectButton(this, "yes");

        updateResult();
    });

    // =========================================
    // Q3 NO
    // =========================================

    document.querySelector(".wl-q3-no")?.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();

        answers.q3 = "no";

        selectButton(this, "no");

        updateResult();
    });

    // =========================================
    // Q4 YES
    // =========================================

    document.querySelector(".wl-q4-yes")?.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();

        answers.q4 = "yes";

        selectButton(this, "yes");

        updateResult();
    });

    // =========================================
    // Q4 NO
    // =========================================

    document.querySelector(".wl-q4-no")?.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();

        answers.q4 = "no";

        selectButton(this, "no");

        updateResult();
    });

    // =========================================
    // INITIAL STATE
    // =========================================

    hideResult();

    console.log("Work Licence question system loaded.");

});


// K js foe=r this quation
document.addEventListener('DOMContentLoaded', function () {

    const questions = document.querySelectorAll('.sh-question');
    const progressItems = document.querySelectorAll('.sh-progress-item');

    console.log('Questions found:', questions.length);

    function showStep(step) {

        questions.forEach(function (question, index) {
            question.classList.toggle('is-active', index === step);
        });

        progressItems.forEach(function (item, index) {
            item.classList.toggle('is-active', index <= step);
        });

    }

    document.addEventListener('click', function (event) {

        const option = event.target.closest('.sh-option');

        if (!option) return;

        const question = option.dataset.question;
        const value = option.dataset.value;

        console.log('Clicked:', question, value);

        /* Q1 */

        if (question === 'why') {

            if (value === 'demerit' || value === 'highspeed') {
                showStep(1);
                return;
            }

            if (value === 'drink') {
                console.log('Drink → result');
                return;
            }

            if (value === 'sper') {
                console.log('SPER → result');
                return;
            }

            if (value === 'other') {
                console.log('Other → result');
                return;
            }
        }


        /* Q2 */

        if (question === 'licence') {

            if (value === 'yes') {
                showStep(2);
                return;
            }

            if (value === 'no') {
                console.log('Licence NO → result');
                return;
            }
        }

    });


    /* Start with Q1 */

    showStep(0);

});





// Dhruv 4 question 


document.addEventListener("DOMContentLoaded", function () {

    const answers = {
        q1: null,
        q2: null,
        q3: null,
        q4: null
    };

    const resultBox = document.querySelector(".licence-result");

    function selectButton(button, type) {
        const question = button.closest(".question-row");
        if (!question) return;

        question.querySelectorAll(".answer-btn").forEach(function (btn) {
            btn.classList.remove("selected-yes", "selected-no");
        });

        button.classList.add(type === "yes" ? "selected-yes" : "selected-no");
    }

    function updateResult() {

        if (!resultBox) return;

        const allAnswered = Object.values(answers).every(v => v !== null);

        if (!allAnswered) {
            resultBox.classList.remove("show-result", "result-success", "result-danger");
            resultBox.innerHTML = "";
            return;
        }

        resultBox.classList.add("show-result");

        if (Object.values(answers).includes("no")) {

            resultBox.classList.remove("result-success");
            resultBox.classList.add("result-danger");

            resultBox.innerHTML =
                `A court disqualification in the last 5 years generally rules out a special hardship licence. It's worth confirming the exact dates with us before assuming it's closed off — call <a href="tel:1300952255" class="phone-link">1300 952 255</a>.`;

        } else {

            resultBox.classList.remove("result-danger");
            resultBox.classList.add("result-success");

            resultBox.innerHTML =
                `You look like you may qualify for a special hardship licence — the legal way to keep driving instead of risking an unlicensed driving charge. Don't drive on the suspension in the meantime. Call <a href="tel:1300952255" class="phone-link">1300 952 255</a> or see our full guide.`;
        }
    }

    document.querySelectorAll(".question-row").forEach(function (question, index) {

        const yesBtn = question.querySelector(".answer-btn.yes");
        const noBtn = question.querySelector(".answer-btn.no");

        const key = "q" + (index + 1);

        if (yesBtn) {
            yesBtn.addEventListener("click", function (e) {
                e.preventDefault();
                answers[key] = "yes";
                selectButton(yesBtn, "yes");
                updateResult();
            });
        }

        if (noBtn) {
            noBtn.addEventListener("click", function (e) {
                e.preventDefault();
                answers[key] = "no";
                selectButton(noBtn, "no");
                updateResult();
            });
        }

    });

});


jQuery(document).ready(function ($) {

    var $section = $('.work-licence-question');

    /*
    |--------------------------------------------------------------------------
    | RESULT CONTENT
    |--------------------------------------------------------------------------
    */

    var results = {

        eligible: `
            <div class="richlands-result richlands-success">
                <p>
                   You look eligible to apply — but it must be filed before you are sentenced.
                    Call 1300 952 255 before your court date so nothing is lost.
                </p>
            </div>
        `,

        mayNotQualify: `
            <div class="richlands-result richlands-warning">
                <p>
                    You may not qualify — a work licence needs an open licence and a clean recent record.
                    Edge cases exist, so call 1300 952 255 before ruling it out.
                </p>
            </div>
        `,

        highRange: `
            <div class="richlands-result richlands-danger">
                <p>
                    A reading of 0.15 or more is high range — no work licence is available at any court.
                    Preparation still decides how far past the 6-month minimum your disqualification goes.
                    Call 1300 952 255, or read our high range guide.
                </p>
            </div>
        `
    };


    /*
    |--------------------------------------------------------------------------
    | FIND ALL 3 QUESTIONS
    |--------------------------------------------------------------------------
    */

    $section.find('> .e-con').each(function () {

        var $question = $(this);

        // Don't treat result container as a question
        if ($question.hasClass('result-richlands')) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | YES / NO BUTTONS
        |--------------------------------------------------------------------------
        */

        var $buttons = $question.find('.elementor-widget-button');


        $buttons.each(function () {

            var $buttonWidget = $(this);
            var $button = $buttonWidget.find('.elementor-button');

            var answer = $.trim(
                $button.find('.elementor-button-text').text()
            ).toLowerCase();


            // Store yes/no on button
            $buttonWidget.attr('data-answer', answer);


            /*
            |--------------------------------------------------------------------------
            | BUTTON CLICK
            |--------------------------------------------------------------------------
            */

            $button.on('click', function (e) {

                e.preventDefault();


                // Remove selected state from both buttons
                $buttons.removeClass('selected');


                // Select clicked button
                $buttonWidget.addClass('selected');


                // Save answer on question
                $question.attr('data-answer', answer);


                // Update result
                updateResult();

            });

        });

    });


    /*
    |--------------------------------------------------------------------------
    | UPDATE RESULT
    |--------------------------------------------------------------------------
    */

    function updateResult() {

        var answers = [];


        /*
        |--------------------------------------------------------------------------
        | GET ANSWERS FROM 3 QUESTIONS
        |--------------------------------------------------------------------------
        */

        $section.find('> .e-con').each(function () {

            var $question = $(this);


            // Skip result container
            if ($question.hasClass('result-richlands')) {
                return;
            }


            answers.push(
                $question.attr('data-answer') || ''
            );

        });


        /*
        |--------------------------------------------------------------------------
        | DON'T SHOW RESULT UNTIL ALL 3 ARE ANSWERED
        |--------------------------------------------------------------------------
        */

        if (
            answers.length !== 3 ||
            answers.indexOf('') !== -1
        ) {

            $section.find('.result-richlands').empty();

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | COUNT YES ANSWERS
        |--------------------------------------------------------------------------
        */

        var yesCount = answers.filter(function (answer) {

            return answer === 'yes';

        }).length;


        var result = '';


        /*
        |--------------------------------------------------------------------------
        | 3 YES
        | ELIGIBLE
        |--------------------------------------------------------------------------
        */

        if (yesCount === 3) {

            result = results.eligible;

        }


        /*
        |--------------------------------------------------------------------------
        | 1 OR 2 YES
        | MAY NOT QUALIFY
        |--------------------------------------------------------------------------
        */

        else if (yesCount === 1 || yesCount === 2) {

            result = results.mayNotQualify;

        }


        /*
        |--------------------------------------------------------------------------
        | 3 NO
        | HIGH RANGE
        |--------------------------------------------------------------------------
        */

        else if (yesCount === 0) {

            result = results.highRange;

        }


        /*
        |--------------------------------------------------------------------------
        | SHOW RESULT
        |--------------------------------------------------------------------------
        */

        $section
            .find('.result-richlands')
            .html(result);

    }

});
