module Claspx
  class Cmd
    def initialize(name)
      @name = name
    end

    def execute(cmdline, dir=nil)
      if dir.nil?
        p "Cmd.execute 1 cmdline=#{cmdline}"
        o, e, s = Open3.capture3(cmdline)
      else
        p "Cmd.execute 2 cmdline=#{cmdline} | chdir: #{dir}"
        o, e, s = Open3.capture3(cmdline, chdir: dir)
      end

      if s.success?
        if dir.nil?
          p "Cmd.execute success cmdline=#{cmdline}"
        else
          p "Cmd.execute success cmdline=#{cmdline} | dir=#{dir}"
        end
        stdout = o.chomp
        result = Result.new(true, stdout)
        result.add_std_out_and_err(stdout, e)
        result.add_return_value(stdout)
      else
        if dir.nil?
          p "Cmd.execute error cmdline=#{cmdline}"
        else
          p "Cmd.execute error cmdline=#{cmdline} | dir=#{dir}"
        end
        stderr = e.chomp
        result = Result.new(false, stderr)
        result.add_std_out_and_err(o, e)
        result.add_return_value(stderr)
      end
    end

    def spawn(cmd_line, &block)
      if block_given?
        PTY.spawn(cmd_line, &block)
      else
        Result.new(true, "xxxx no_block cmd_line=#{cmd_line}")
      end
    end
  end
end