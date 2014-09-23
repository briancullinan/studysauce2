<div class="panel-pane" id="schedule">

    <div class="pane-content">

        <h2>Enter your class below</h2>

        <div class="school-name">
            <label class="input">
                School name
                <input type="text" placeholder="Enter the full name" autocomplete="off">
            </label>
        </div>

        <header>
            <label>Class name</label>

            <div class="day-of-the-week">
                <label>M</label>
                <label>Tu</label>
                <label>W</label>
                <label>Th</label>
                <label>F</label>
                <label>Sa</label>
                <label>Su</label>
            </div>
            <label>Time</label>
            <label>&nbsp;Date</label>
        </header>
        <div class="schedule clearfix">
            <div class="class-row valid clearfix" id="eid-7480">
                <div class="class-name">
                    <label class="input">
                        Class name<br/>
                        <input type="text" placeholder="BUS 300" autocomplete="off">
                    </label>
                </div>
                <div class="day-of-the-week">
                    <label class="checkbox">M<br/><input type="checkbox" name="schedule-dotw-M"
                                                         checked="checked"><i></i></label>
                    <label class="checkbox">Tu<br/><input type="checkbox" name="schedule-dotw-Tu"><i></i></label>
                    <label class="checkbox">W<br/><input type="checkbox" name="schedule-dotw-W"><i></i></label>
                    <label class="checkbox">Th<br/><input type="checkbox" name="schedule-dotw-Th"><i></i></label>
                    <label class="checkbox">F<br/><input type="checkbox" name="schedule-dotw-F"><i></i></label>
                    <label class="checkbox">Sa<br/><input type="checkbox" name="schedule-dotw-Sa"><i></i></label>
                    <label class="checkbox">Su<br/><input type="checkbox" name="schedule-dotw-Su"><i></i></label>
                </div>
                <div class="start-time">
                    <label class="input">
                        Time<br/>
                        <input type="text" placeholder="Start" title="What time does your class begin?"
                               autocomplete="off">
                        <input type="time" placeholder="Start" title="What time does your class begin?"
                               autocomplete="off">
                    </label>
                </div>
                <div class="end-time">
                    <label class="input">
                        &nbsp;<br/>
                        <input type="text" placeholder="End" title="What time does your class end?" autocomplete="off">
                        <input type="time" placeholder="End" title="What time does your class end?" autocomplete="off">
                    </label>
                </div>
                <div class="start-date">
                    <label class="input">
                        Date<br/>
                        <input type="text" placeholder="First class" title="What day does your academic term begin?"
                               autocomplete="off">
                    </label>
                </div>
                <div class="end-date">
                    <label class="input">
                        &nbsp;<br/>
                        <input type="text" placeholder="Last class" title="What day does your academic term end?"
                               autocomplete="off">
                    </label>
                </div>
                <input type="hidden" class="field-type-hidden field-name-field-event-type " name="schedule-type"
                       value="c">

                <div class="read-only">&nbsp;<br/><a href="#edit-class">&nbsp;</a>
                    <a href="#remove-class">&nbsp;</a></div>
                <div class="invalid-times">Error - invalid class time</div>
                <div class="overlaps-only">Error - classes cannot overlap</div>
                <div class="invalid-only">Error - please make sure all class information is filled in</div>
            </div>
        </div>

        <p class="class-actions highlighted-link">
            <a href="#add-class">Add <span>+</span> class</a>
            <a href="#save-class" class="more">Save</a>
        </p>
        <hr/>
        <h2>Enter work or other recurring obligations here</h2>

        <header>
            <label>Class name</label>

            <div class="day-of-the-week">
                <label>M</label>
                <label>Tu</label>
                <label>W</label>
                <label>Th</label>
                <label>F</label>
                <label>Sa</label>
                <label>Su</label>
            </div>
            <label>Time</label>
            <label>&nbsp;Date</label>
        </header>
        <div class="schedule other">
            <div class="class-row valid clearfix" id="eid-7480">
                <div class="class-name">
                    <label class="input">
                        Class name<br/>
                        <input type="text" placeholder="BUS 300" autocomplete="off">
                    </label>
                </div>
                <div class="day-of-the-week">
                    <label class="checkbox">M<br/><input type="checkbox" name="schedule-dotw-M"
                                                         checked="checked"><i></i></label>
                    <label class="checkbox">Tu<br/><input type="checkbox" name="schedule-dotw-Tu"><i></i></label>
                    <label class="checkbox">W<br/><input type="checkbox" name="schedule-dotw-W"><i></i></label>
                    <label class="checkbox">Th<br/><input type="checkbox" name="schedule-dotw-Th"><i></i></label>
                    <label class="checkbox">F<br/><input type="checkbox" name="schedule-dotw-F"><i></i></label>
                    <label class="checkbox">Sa<br/><input type="checkbox" name="schedule-dotw-Sa"><i></i></label>
                    <label class="checkbox">Su<br/><input type="checkbox" name="schedule-dotw-Su"><i></i></label>

                    <div class="recurring">
                        <label class="checkbox">Recurring<input type="checkbox" name="schedule-dotw-M"
                                                                checked="checked"><i></i>Weekly</label>
                    </div>
                </div>
                <div class="start-time">
                    <label class="input">
                        Time<br/>
                        <input type="text" placeholder="Start" title="What time does your class begin?"
                               autocomplete="off">
                        <input type="time" placeholder="Start" title="What time does your class begin?"
                               autocomplete="off">
                    </label>
                </div>
                <div class="end-time">
                    <label class="input">
                        &nbsp;<br/>
                        <input type="text" placeholder="End" title="What time does your class end?" autocomplete="off">
                        <input type="time" placeholder="End" title="What time does your class end?" autocomplete="off">
                    </label>
                </div>
                <div class="start-date">
                    <label class="input">
                        Date<br/>
                        <input type="text" placeholder="First class" title="What day does your academic term begin?"
                               autocomplete="off">
                    </label>
                </div>
                <div class="end-date">
                    <label class="input">
                        &nbsp;<br/>
                        <input type="text" placeholder="Last class" title="What day does your academic term end?"
                               autocomplete="off">
                    </label>
                </div>
                <input type="hidden" class="field-type-hidden field-name-field-event-type " name="schedule-type"
                       value="c">

                <div class="read-only">&nbsp;<br/><a href="#edit-class">&nbsp;</a>
                    <a href="#remove-class">&nbsp;</a></div>
                <div class="invalid-times">Error - invalid class time</div>
                <div class="overlaps-only">Error - classes cannot overlap</div>
                <div class="invalid-only">Error - please make sure all class information is filled in</div>
            </div>
        </div>

        <p class="other-actions highlighted-link">
            <a href="#add-class">Add <span>+</span> other event</a>
            <a href="#save-class" class="more">Save</a>
        </p>

    </div>

</div>