<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<div class="poll-header">
    <h3><?=$arResult["VOTE"]["DESCRIPTION"]?></h3>
    <img src="<?=$arResult['VOTE']['IMAGE']['SRC']?>" width="500" height="300">
</div>

<form id="poll-form" action="<?=POST_FORM_ACTION_URI?>" method="post" class="vote-form">

	<input type="hidden" name="vote" value="Y">
	<input type="hidden" name="PUBLIC_VOTE_ID" value="<?=$arResult["VOTE"]["ID"]?>">
	<input type="hidden" name="VOTE_ID" value="<?=$arResult["VOTE"]["ID"]?>">
	<?=bitrix_sessid_post()?>

    <div class="owl-carousel" id="question-carousel">
        <?foreach ($arResult["QUESTIONS"] as $arQuestion): ?>
            <div class="poll-question">
                <p><?=$arQuestion["QUESTION"]?></p>
                <ul class="answers">
                    <?foreach ($arQuestion["ANSWERS"] as $arAnswer): ?>
                        <li>
                            <label for="vote_radio_<?=$arAnswer["QUESTION_ID"]?>_<?=$arAnswer["ID"]?>">
							<input type="radio" <?=$value?> name="vote_radio_<?=$arAnswer["QUESTION_ID"]?>" <?
								?>id="vote_radio_<?=$arAnswer["QUESTION_ID"]?>_<?=$arAnswer["ID"]?>" <?
								?>value="<?=$arAnswer["ID"]?>" <?=$arAnswer["~FIELD_PARAM"]?> />
                                <?=$arAnswer["MESSAGE"]?>
                            </label>
                        </li>
                    <?endforeach;?>
                </ul>
            </div>
        <?endforeach;?>
    </div>

    <input type="button" id="prev-button" value="Назад" class="btn">
    <input type="button" id="next-button" value="Далее" class="btn">
    <input type="submit" name="vote" value="<?=GetMessage("VOTE_SUBMIT_BUTTON")?>" class="btn">
</form>

<div id="success-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p>Спасибо за участие в опросе!</p>
    </div>
</div>

<script>
$(document).ready(function(){
    var currentStep = 0;
    var $carousel = $("#question-carousel");
    var $prevButton = $("#prev-button");
    var $nextButton = $("#next-button");
    var $submitButton = $("[name='vote']");

    $carousel.owlCarousel({
        items: 1,
        loop: false,
        nav: false,
        dots: false,
        touchDrag: false,
        mouseDrag: false,
        onInitialized: function() {
            updateButtonsVisibility();
        }
    });

    $prevButton.hide();

    $nextButton.click(function() {
        if (validateCurrentStep()) {
            if (currentStep < <?= count($arResult["QUESTIONS"]) - 1 ?>) {
                currentStep++;
                $carousel.trigger("next.owl.carousel");
                updateButtonsVisibility();
            }
        } else {
            alert("Пожалуйста, выберите ответ на текущий вопрос.");
        }
    });

    $prevButton.click(function() {
        if (currentStep > 0) {
            currentStep--;
            $carousel.trigger("prev.owl.carousel");
            updateButtonsVisibility();
        }
    });

    function updateButtonsVisibility() {
        $prevButton.toggle(currentStep > 0);
        $nextButton.toggle(currentStep < <?= count($arResult["QUESTIONS"]) - 1 ?>);
    }

    function validateCurrentStep() {
        var $currentQuestion = $($carousel.find(".poll-question")[currentStep]);
        var $selectedAnswer = $currentQuestion.find("input[type='radio']:checked");
        return $selectedAnswer.length > 0;
    }

    $("#poll-form").submit(function(e) {
        var allQuestionsAnswered = true;
        $(".owl-carousel .owl-item").each(function(index) {
            if ($(this).find("input[type=radio]:checked").length === 0) {
                allQuestionsAnswered = false;
                return false;
            }
        });

        if (allQuestionsAnswered) {
            $("#success-modal").css("display", "block");

            $(".close, .modal").click(function() {
                $("#success-modal").css("display", "none");
				window.location.href = 'https://beloglazov.local/rezultaty-oprosa.php';
            });

            $(document).keydown(function(e) {
                if (e.key === "Escape") {
                    $("#success-modal").css("display", "none");
					window.location.href = 'https://beloglazov.local/rezultaty-oprosa.php';
                }
            });
        } else {
            alert("Пожалуйста, ответьте на все вопросы перед отправкой.");
        }
    });
});
</script>
