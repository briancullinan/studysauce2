<div class="panel-pane" id="deadlines">

    <div class="pane-content">

        <h2>Enter important dates and we will send you email reminders</h2>

        <a href="#add-deadline" class="field-add-more-submit ajax-processed" name="field_reminders_add_more">
            Add <span>+</span> deadline
        </a>

        <div class="sort-by">
            <label>Sort by: </label>
            <label class="radio"><input type="radio" name="deadlines-sort" value="date" checked="checked" /><i></i>Date</label>
            <label class="radio"><input type="radio" name="deadlines-sort" value="class"><i></i>Class</label>
            <label class="checkbox" title="Click here to see deadlines that have already passed."><input type="checkbox"><i></i>Past deadlines</label>
        </div>

        <div class="head hide">1 June <span>2014</span></div>
        <div class="deadline-row first valid">
            <div class="class-name">
                <label class="select">
                    Class name<br/>
                    <select size="1">
                        <option value="_none">- Select a class -</option>
                        <option value="CHEM 101">CHEM 101</option>
                        <option value="HIST 101">HIST 101</option>
                        <option value="STAT 101" selected="">STAT 101</option>
                        <option value="PHYS 101">PHYS 101</option>
                        <option value="SPAN 101">SPAN 101</option>
                        <option value="ECON 101">ECON 101</option>
                        <option value="Nonacademic">Nonacademic</option>
                    </select>
                </label>
            </div>
            <div class="assignment">
                <label class="select">
                    Assignment<br/>
                    <input placeholder="Paper, exam, project, etc." type="text" value="Group project" size="60" maxlength="255">
                </label>
            </div>
            <div class="day-of-the-week">
                <label>Reminders</label>
                <label class="checkbox"><input type="checkbox" value="1209600" checked="checked"><i></i><br/>2 wk</label>
                <label class="checkbox"><input type="checkbox" value="604800"><i></i><br/>1 wk</label>
                <label class="checkbox"><input type="checkbox" value="345600"><i></i><br/>4 days</label>
                <label class="checkbox"><input type="checkbox" value="172800"><i></i><br/>2 days</label>
                <label class="checkbox"><input type="checkbox" value="86400"><i></i><br/>1 day</label>
            </div>
            <div class="assignment">
                <label class="input">
                    Due date<br/>
                    <input placeholder="Enter date" type="text" value="06/01/2014" size="60" maxlength="255">
                </label>
            </div>
            <div class="assignment">
                <label class="input">
                    % of grade<br/>
                    <input type="text" value="15%" size="60" maxlength="255">
                </label>
            </div>
            <div class="read-only">
                <a href="#edit-reminder">&nbsp;</a><a href="#remove-reminder">&nbsp;</a>
            </div>
        </div>

        <div class="highlighted-link">
            <a href="/schedule">Edit schedule</a><a href="#save-dates" class="more">Save</a>
        </div>

    </div>

</div>