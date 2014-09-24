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
            <div class="class-row valid clearfix">
                <div class="class-name">
                    <label class="input">
                        <span>Class name</span>
                        <input type="text" placeholder="BUS 300" autocomplete="off">
                    </label>
                </div>
                <div class="day-of-the-week">
                    <label class="checkbox"><span>M</span><input type="checkbox" value="M" checked="checked"><i></i></label>
                    <label class="checkbox"><span>Tu</span><input type="checkbox" value="Tu"><i></i></label>
                    <label class="checkbox"><span>W</span><input type="checkbox" value="W"><i></i></label>
                    <label class="checkbox"><span>Th</span><input type="checkbox" value="Th"><i></i></label>
                    <label class="checkbox"><span>F</span><input type="checkbox" value="F"><i></i></label>
                    <label class="checkbox"><span>Sa</span><input type="checkbox" value="Sa"><i></i></label>
                    <label class="checkbox"><span>Su</span><input type="checkbox" value="Su"><i></i></label>
                </div>
                <div class="start-time">
                    <label class="input">
                        <span>Time</span>
                        <input type="text" placeholder="Start" title="What time does your class begin?"
                               autocomplete="off">
                    </label>
                    <label class="input mobile-only">
                        <span>Time</span>
                        <input type="time" title="What time does your class begin?"
                               autocomplete="off">
                    </label>
                </div>
                <div class="end-time">
                    <label class="input">
                        <span>&nbsp;</span>
                        <input type="text" placeholder="End" title="What time does your class end?" autocomplete="off">
                    </label>
                    <label class="input mobile-only">
                        <span>&nbsp;</span>
                        <input type="time" title="What time does your class end?" autocomplete="off">
                    </label>
                </div>
                <div class="start-date">
                    <label class="input">
                        <span>Date</span>
                        <input type="text" placeholder="First class" title="What day does your academic term begin?"
                               autocomplete="off">
                    </label>
                </div>
                <div class="end-date">
                    <label class="input">
                        <span>&nbsp;</span>
                        <input type="text" placeholder="Last class" title="What day does your academic term end?"
                               autocomplete="off">
                    </label>
                </div>
                <input type="hidden" name="event-type" value="c">

                <div class="read-only"><a href="#edit-class">&nbsp;</a><a href="#remove-class">&nbsp;</a></div>
                <div class="invalid-times">Error - invalid class time</div>
                <div class="overlaps-only">Error - classes cannot overlap</div>
                <div class="invalid-only">Error - please make sure all class information is filled in</div>
            </div>
        </div>

        <div class="class-actions highlighted-link">
            <a href="#add-class">Add <span>+</span> class</a>
            <a href="#save-class" class="more">Save</a>
        </div>
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
            <div class="class-row valid clearfix read-only">
                <div class="class-name">
                    <label class="input">
                        <span>Class name</span>
                        <input type="text" placeholder="BUS 300" autocomplete="off">
                    </label>
                </div>
                <div class="day-of-the-week">
                    <label class="checkbox"><span>M</span><input type="checkbox" value="M" checked="checked"><i></i></label>
                    <label class="checkbox"><span>Tu</span><input type="checkbox" value="Tu"><i></i></label>
                    <label class="checkbox"><span>W</span><input type="checkbox" value="W"><i></i></label>
                    <label class="checkbox"><span>Th</span><input type="checkbox" value="Th"><i></i></label>
                    <label class="checkbox"><span>F</span><input type="checkbox" value="F"><i></i></label>
                    <label class="checkbox"><span>Sa</span><input type="checkbox" value="Sa"><i></i></label>
                    <label class="checkbox"><span>Su</span><input type="checkbox" value="Su"><i></i></label>

                    <div class="recurring">
                        <label class="checkbox">Recurring<input type="checkbox" value="Weekly"
                                                                checked="checked"><i></i>Weekly</label>
                    </div>
                </div>
                <div class="start-time">
                    <div class="input">
                        <label><span>Time</span>
                        <input type="text" placeholder="Start" title="What time does your class begin?"
                               autocomplete="off">
                        </label>
                    </div>
                    <div class="input mobile-only">
                        <label><span>Time</span>
                            <input type="time" title="What time does your class begin?"
                                   autocomplete="off">
                        </label>
                    </div>
                </div>
                <div class="end-time">
                    <label class="input">
                        <span>&nbsp;</span>
                        <input type="text" placeholder="End" title="What time does your class end?" autocomplete="off">
                    </label>
                    <label class="input mobile-only">
                        <span>&nbsp;</span>
                        <input type="time" title="What time does your class end?" autocomplete="off">
                    </label>
                </div>
                <div class="start-date">
                    <label class="input">
                        <span>Date</span>
                        <input type="text" placeholder="First class" title="What day does your academic term begin?"
                               autocomplete="off">
                    </label>
                </div>
                <div class="end-date">
                    <label class="input">
                        <span>&nbsp;</span>
                        <input type="text" placeholder="Last class" title="What day does your academic term end?"
                               autocomplete="off">
                    </label>
                </div>
                <input type="hidden" name="event-type" value="o">

                <div class="read-only"><a href="#edit-class">&nbsp;</a><a href="#remove-class">&nbsp;</a></div>
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