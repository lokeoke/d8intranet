<?php

/* core/modules/system/templates/status-report.html.twig */
class __TwigTemplate_c832108177659325a98151203ce429409d0a527c8ad72c4d40d9b8fbd4db58cf extends Twig_Template
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
        // line 20
        echo "<table class=\"system-status-report\">
  ";
        // line 21
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["requirements"]) ? $context["requirements"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["requirement"]) {
            // line 22
            echo "    <tr class=\"system-status-report__entry system-status-report__entry--";
            echo $this->env->getExtension('drupal_core')->escapeFilter($this->env, $this->getAttribute($context["requirement"], "severity_status", array()), "html", null, true);
            echo " color-";
            echo $this->env->getExtension('drupal_core')->escapeFilter($this->env, $this->getAttribute($context["requirement"], "severity_status", array()), "html", null, true);
            echo "\">
      ";
            // line 23
            if (twig_in_filter($this->getAttribute($context["requirement"], "severity_status", array()), array(0 => "warning", 1 => "error"))) {
                // line 24
                echo "        <th class=\"system-status-report__status-title system-status-report__status-icon system-status-report__status-icon--";
                echo $this->env->getExtension('drupal_core')->escapeFilter($this->env, $this->getAttribute($context["requirement"], "severity_status", array()), "html", null, true);
                echo "\">
          <span class=\"visually-hidden\">";
                // line 25
                echo $this->env->getExtension('drupal_core')->escapeFilter($this->env, $this->getAttribute($context["requirement"], "severity_title", array()), "html", null, true);
                echo "</span>
      ";
            } else {
                // line 27
                echo "        <th class=\"system-status-report__status-title\">
      ";
            }
            // line 29
            echo "        ";
            echo $this->env->getExtension('drupal_core')->escapeFilter($this->env, $this->getAttribute($context["requirement"], "title", array()), "html", null, true);
            echo "
      </th>
      <td>
        ";
            // line 32
            echo $this->env->getExtension('drupal_core')->escapeFilter($this->env, $this->getAttribute($context["requirement"], "value", array()), "html", null, true);
            echo "
        ";
            // line 33
            if ($this->getAttribute($context["requirement"], "description", array())) {
                // line 34
                echo "          <div class=\"description\">";
                echo $this->env->getExtension('drupal_core')->escapeFilter($this->env, $this->getAttribute($context["requirement"], "description", array()), "html", null, true);
                echo "</div>
        ";
            }
            // line 36
            echo "      </td>
    </tr>
  ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['requirement'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 39
        echo "  </tbody>
</table>
";
    }

    public function getTemplateName()
    {
        return "core/modules/system/templates/status-report.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  76 => 39,  68 => 36,  62 => 34,  60 => 33,  56 => 32,  49 => 29,  45 => 27,  40 => 25,  35 => 24,  33 => 23,  26 => 22,  22 => 21,  19 => 20,);
    }
}
/* {#*/
/* /***/
/*  * @file*/
/*  * Default theme implementation for the status report.*/
/*  **/
/*  * Available variables:*/
/*  * - requirements: Contains multiple requirement instances.*/
/*  *   Each requirement contains:*/
/*  *   - title: The title of the requirement.*/
/*  *   - value: (optional) The requirement's status.*/
/*  *   - description: (optional) The requirement's description.*/
/*  *   - severity_title: The title of the severity.*/
/*  *   - severity_status: Indicates the severity status.*/
/*  **/
/*  * @see template_preprocess_status_report()*/
/*  **/
/*  * @ingroup themeable*/
/*  *//* */
/* #}*/
/* <table class="system-status-report">*/
/*   {% for requirement in requirements %}*/
/*     <tr class="system-status-report__entry system-status-report__entry--{{ requirement.severity_status }} color-{{ requirement.severity_status }}">*/
/*       {% if requirement.severity_status in ['warning', 'error'] %}*/
/*         <th class="system-status-report__status-title system-status-report__status-icon system-status-report__status-icon--{{ requirement.severity_status }}">*/
/*           <span class="visually-hidden">{{ requirement.severity_title }}</span>*/
/*       {% else %}*/
/*         <th class="system-status-report__status-title">*/
/*       {% endif %}*/
/*         {{ requirement.title }}*/
/*       </th>*/
/*       <td>*/
/*         {{ requirement.value }}*/
/*         {% if requirement.description %}*/
/*           <div class="description">{{ requirement.description }}</div>*/
/*         {% endif %}*/
/*       </td>*/
/*     </tr>*/
/*   {% endfor %}*/
/*   </tbody>*/
/* </table>*/
/* */
