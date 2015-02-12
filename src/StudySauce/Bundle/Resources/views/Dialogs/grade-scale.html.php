<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Grade Scale at your school
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<ul class="nav nav-tabs">
    <li class="active"><a href="#extended-scale" data-target="#extended-scale" data-toggle="tab">A +/-</a></li>
    <li><a href="#abbreviated-scale" data-target="#abbreviated-scale" data-toggle="tab">A</a></li>
</ul>
<div class="tab-content">
    <div id="extended-scale" class="tab-pane active">
        <table>
            <thead>
            <tr>
                <th></th>
                <th>High</th>
                <th>Low</th>
                <th>Grade point</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>A+</td>
                <td>100</td>
                <td>97</td>
                <td>4.0</td>
            </tr>
            <tr>
                <td>A</td>
                <td>96</td>
                <td>93</td>
                <td>4.0</td>
            </tr>
            <tr>
                <td>A-</td>
                <td>92</td>
                <td>90</td>
                <td>3.7</td>
            </tr>
            <tr>
                <td>B+</td>
                <td>89</td>
                <td>87</td>
                <td>3.3</td>
            </tr>
            <tr>
                <td>B</td>
                <td>86</td>
                <td>83</td>
                <td>3.0</td>
            </tr>
            <tr>
                <td>B-</td>
                <td>82</td>
                <td>80</td>
                <td>2.7</td>
            </tr>
            <tr>
                <td>C+</td>
                <td>79</td>
                <td>77</td>
                <td>2.3</td>
            </tr>
            <tr>
                <td>C</td>
                <td>76</td>
                <td>73</td>
                <td>2.0</td>
            </tr>
            <tr>
                <td>C-</td>
                <td>72</td>
                <td>70</td>
                <td>1.7</td>
            </tr>
            <tr>
                <td>D+</td>
                <td>69</td>
                <td>67</td>
                <td>1.3</td>
            </tr>
            <tr>
                <td>D</td>
                <td>66</td>
                <td>63</td>
                <td>1.0</td>
            </tr>
            <tr>
                <td>D-</td>
                <td>62</td>
                <td>60</td>
                <td>0.7</td>
            </tr>
            <tr>
                <td>F</td>
                <td>59</td>
                <td>0</td>
                <td>0.0</td>
            </tr>
            </tbody>
        </table>
    </div>
    <div id="abbreviated-scale" class="tab-pane">
        <table>
            <thead>
            <tr>
                <th></th>
                <th>High</th>
                <th>Low</th>
                <th>Grade point</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>A</td>
                <td>100</td>
                <td>90</td>
                <td>4.0</td>
            </tr>
            <tr>
                <td>B</td>
                <td>89</td>
                <td>80</td>
                <td>3.0</td>
            </tr>
            <tr>
                <td>C</td>
                <td>79</td>
                <td>70</td>
                <td>2.0</td>
            </tr>
            <tr>
                <td>D</td>
                <td>69</td>
                <td>60</td>
                <td>1.0</td>
            </tr>
            <tr>
                <td>F</td>
                <td>59</td>
                <td>0</td>
                <td>0.0</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer') ?>
<a href="#save-scale" data-dismiss="modal" class="btn btn-primary">Save</a>
<?php $view['slots']->stop() ?>

