<?php

/* core/modules/update/templates/update-last-check.html.twig */
class __TwigTemplate_d7e50a1c629a2ff0ce308c392b268180ec94c40cd269055ed5c727169e74c0b4 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 16
        echo "<p>
  ";
        // line 17
        if ((isset($context["last"]) ? $context["last"] : null)) {
            // line 18
            echo "    ";
            echo $this->env->getExtension('drupal_core')->renderVar(t("Last checked: @time ago", array("@time" => (isset($context["time"]) ? $context["time"] : null))));
            echo "
  ";
        } else {
            // line 20
            echo "    ";
            echo $this->env->getExtension('drupal_core')->renderVar(t("Last checked: never"));
            echo "
  ";
        }
        // line 22
        echo "  (";
        echo $this->env->getExtension('drupal_core')->escapeFilter($this->env, (isset($context["link"]) ? $context["link"] : null), "html", null, true);
        echo ")
</p>
";
    }

    public function getTemplateName()
    {
        return "core/modules/update/templates/update-last-check.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  36 => 22,  30 => 20,  24 => 18,  22 => 17,  19 => 16,);
    }
}
/* {#*/
/* /***/
/*  * @file*/
/*  * Default theme implementation for the last time update data was checked.*/
/*  **/
/*  * Available variables:*/
/*  * - last: The timestamp that the site was last checked for updates.*/
/*  * - time: The formatted time since the site last checked for updates.*/
/*  * - link: A link to check for updates manually.*/
/*  **/
/*  * @see template_preprocess_update_last_check()*/
/*  **/
/*  * @ingroup themeable*/
/*  *//* */
/* #}*/
/* <p>*/
/*   {% if last %}*/
/*     {{ 'Last checked: @time ago'|t({'@time': time}) }}*/
/*   {% else %}*/
/*     {{ 'Last checked: never'|t }}*/
/*   {% endif %}*/
/*   ({{ link }})*/
/* </p>*/
/* */
